<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Yuja Local Plugin
 * @package    local_yuja
 * @subpackage yuja
 * @copyright  2016 YuJa
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use Firebase\JWT\JWT;

class auth_helper {
    /**
     * Builds a standard LTI Content-Item selection request.
     *
     * @param int $id The tool type ID.
     * @param stdClass $course The course object.
     * @param string $title The tool's title, if available.
     * @param string $text The text to display to represent the content item. This value may be a long description of the content item.
     * @param array $mediatypes Array of MIME types types supported by the TC. If empty, the TC will support ltilink by default.
     * @param array $presentationtargets Array of ways in which the selected content item(s) can be requested to be opened
     *                                   (via the presentationDocumentTarget element for a returned content item).
     *                                   If empty, "frame", "iframe", and "window" will be supported by default.
     * @param bool $autocreate Indicates whether any content items returned by the TP would be automatically persisted without
     * @param bool $multiple Indicates whether the user should be permitted to select more than one item. False by default.
     *                         any option for the user to cancel the operation. False by default.
     * @param bool $unsigned Indicates whether the TC is willing to accept an unsigned return message, or not.
     *                       A signed message should always be required when the content item is being created automatically in the
     *                       TC without further interaction from the user. False by default.
     * @param bool $canconfirm Flag for can_confirm parameter. False by default.
     * @param bool $copyadvice Indicates whether the TC is able and willing to make a local copy of a content item. False by default.
     * @param string $nonce
     * @return stdClass The object containing the signed request parameters and the URL to the TP's Content-Item selection interface.
     * @throws moodle_exception When the LTI tool type does not exist.`
     * @throws coding_exception For invalid media type and presentation target parameters.
     */
    public static function lti_1p3_build_content_item_selection_request($id, $course, $title = '', $text = '', $mediatypes = [],
                                                    $presentationtargets = [], $autocreate = false, $multiple = true,
                                                    $unsigned = false, $canconfirm = false, $copyadvice = false, $nonce = '') {
        global $USER;

        $tool = lti_get_type($id);
        // Validate parameters.
        if (!$tool) {
            throw new moodle_exception('errortooltypenotfound', 'mod_lti');
        }
        if (!is_array($mediatypes)) {
            throw new coding_exception('The list of accepted media types should be in an array');
        }
        if (!is_array($presentationtargets)) {
            throw new coding_exception('The list of accepted presentation targets should be in an array');
        }

        // Check title. If empty, use the tool's name.
        if (empty($title)) {
            $title = $tool->name;
        }

        $typeconfig = lti_get_type_config($id);
        $key = '';
        $secret = '';
        $toolproxy = null;
        if (!empty($tool->clientid)) {
            $key = $tool->clientid;
        } 

        if (!empty($typeconfig['password'])) {
            $secret = $typeconfig['password'];
        }

        $tool->enabledcapability = '';
        if (!empty($typeconfig['enabledcapability_ContentItemSelectionRequest'])) {
            $tool->enabledcapability = $typeconfig['enabledcapability_ContentItemSelectionRequest'];
        }

        $tool->parameter = '';
        if (!empty($typeconfig['parameter_ContentItemSelectionRequest'])) {
            $tool->parameter = $typeconfig['parameter_ContentItemSelectionRequest'];
        }

        // Set the tool URL.
        if (!empty($typeconfig['toolurl_ContentItemSelectionRequest'])) {
            $toolurl = new moodle_url($typeconfig['toolurl_ContentItemSelectionRequest']);
        } else {
            $toolurl = new moodle_url($typeconfig['toolurl']);
        }

        // Check if SSL is forced.
        if (!empty($typeconfig['forcessl'])) {
            // Make sure the tool URL is set to https.
            if (strtolower($toolurl->get_scheme()) === 'http') {
                $toolurl->set_scheme('https');
            }
        }
        $toolurlout = $toolurl->out(false);

        // Get base request parameters.
        $instance = new stdClass();
        $instance->course = $course->id;
        $requestparams = lti_build_request($instance, $typeconfig, $course, $id, false);


        // Get standard request parameters and merge to the request parameters.
        $orgid = lti_get_organizationid($typeconfig);
        $standardparams = lti_build_standard_message(null, $orgid, $tool->ltiversion, 'ContentItemSelectionRequest');
        $requestparams = array_merge($requestparams, $standardparams);

        // Get custom request parameters and merge to the request parameters.
        $customstr = '';
        if (!empty($typeconfig['customparameters'])) {
            $customstr = $typeconfig['customparameters'];
        }
        $customparams = lti_build_custom_parameters($toolproxy, $tool, $instance, $requestparams, $customstr, '', false);
        $requestparams = array_merge($requestparams, $customparams);

        // Add the parameters configured by the LTI services.
        if ($id) {
            $services = lti_get_services();
            foreach ($services as $service) {
                $serviceparameters = $service->get_launch_parameters('ContentItemSelectionRequest',
                    $course->id, $USER->id , $id);
                foreach ($serviceparameters as $paramkey => $paramvalue) {
                    $requestparams['custom_' . $paramkey] = lti_parse_custom_parameter($toolproxy, $tool, $requestparams, $paramvalue,
                        false);
                }
            }
        }

        // Allow request params to be updated by sub-plugins.
        $plugins = core_component::get_plugin_list('ltisource');
        foreach (array_keys($plugins) as $plugin) {
            $pluginparams = component_callback('ltisource_' . $plugin, 'before_launch', [$instance, $toolurlout, $requestparams], []);

            if (!empty($pluginparams) && is_array($pluginparams)) {
                $requestparams = array_merge($requestparams, $pluginparams);
            }
        }

        // Only LTI links are currently supported.
        $requestparams['accept_types'] = 'ltiResourceLink';
        
        // Presentation targets. Supports frame, iframe, window by default if empty.
        if (empty($presentationtargets)) {
            $presentationtargets = [
                'frame',
                'iframe',
                'window',
            ];
        }
        $requestparams['accept_presentation_document_targets'] = implode(',', $presentationtargets);

        // Other request parameters.
        $requestparams['accept_copy_advice'] = $copyadvice === true ? 'true' : 'false';
        $requestparams['accept_multiple'] = $multiple === true ? 'true' : 'false';
        $requestparams['accept_unsigned'] = $unsigned === true ? 'true' : 'false';
        $requestparams['auto_create'] = $autocreate === true ? 'true' : 'false';
        $requestparams['can_confirm'] = $canconfirm === true ? 'true' : 'false';
        $requestparams['title'] = $title;
        $requestparams['text'] = $text;
        $signedparams = auth_helper::lti_1p3_sign_jwt($requestparams, $toolurlout, $key, $id, $nonce);
        
        $toolurlparams = $toolurl->params();

        // Strip querystring params in endpoint url from $signedparams to avoid duplication.
        if (!empty($toolurlparams) && !empty($signedparams)) {
            foreach (array_keys($toolurlparams) as $paramname) {
                if (isset($signedparams[$paramname])) {
                    unset($signedparams[$paramname]);
                }
            }
        }

        // Check for params that should not be passed. Unset if they are set.
        $unwantedparams = [
            'resource_link_id',
            'resource_link_title',
            'resource_link_description',
            'launch_presentation_return_url',
            'lis_result_sourcedid',
        ];
        foreach ($unwantedparams as $param) {
            if (isset($signedparams[$param])) {
                unset($signedparams[$param]);
            }
        }

        // Prepare result object.
        $result = new stdClass();
        $result->params = $signedparams;
        $result->url = $toolurlout;

        return $result;
    }

    /**
     * Converts the message paramters to their equivalent JWT claim and signs the payload to launch the external tool using JWT
     *
     * @param array  $parms        Parameters to be passed for signing
     * @param string $endpoint     url of the external tool
     * @param string $oauthconsumerkey
     * @param string $typeid       ID of LTI tool type
     * @param string $nonce        Nonce value to use
     * @return array|null
     */
    public static function lti_1p3_sign_jwt($parms, $endpoint, $oauthconsumerkey, $typeid = 0, $nonce = '') {
        global $CFG;

        if (empty($typeid)) {
            $typeid = 0;
        }

        $parms['lti_message_type'] = "moodle_media_chooser_plugin";
        
        if (isset($parms['roles'])) {
            $roles = explode(',', $parms['roles']);
            $newroles = array();
            foreach ($roles as $role) {
                if (strpos($role, 'urn:lti:role:ims/lis/') === 0) {
                    $role = 'http://purl.imsglobal.org/vocab/lis/v2/membership#' . substr($role, 21);
                } else if (strpos($role, 'urn:lti:instrole:ims/lis/') === 0) {
                    $role = 'http://purl.imsglobal.org/vocab/lis/v2/institution/person#' . substr($role, 25);
                } else if (strpos($role, 'urn:lti:sysrole:ims/lis/') === 0) {
                    $role = 'http://purl.imsglobal.org/vocab/lis/v2/system/person#' . substr($role, 24);
                } else if ((strpos($role, '://') === false) && (strpos($role, 'urn:') !== 0)) {
                    $role = "http://purl.imsglobal.org/vocab/lis/v2/membership#{$role}";
                }
                $newroles[] = $role;
            }
            $parms['roles'] = implode(',', $newroles);
        }

        $now = time();
        if (empty($nonce)) {
            $nonce = bin2hex(openssl_random_pseudo_bytes(10));
        }
        $claimmapping = lti_get_jwt_claim_mapping();
        $payload = array(
            'nonce' => $nonce,
            'iat' => $now,
            'exp' => $now + 60,
        );
        $payload['iss'] = $CFG->wwwroot;
        $payload['aud'] = $oauthconsumerkey;
        $payload[LTI_JWT_CLAIM_PREFIX . '/claim/deployment_id'] = strval($typeid);
        $payload[LTI_JWT_CLAIM_PREFIX . '/claim/target_link_uri'] = $endpoint;

        $payload['isMoodlePluginLaunch'] = true;
        
        foreach ($parms as $key => $value) {
            $claim = LTI_JWT_CLAIM_PREFIX;
            if (array_key_exists($key, $claimmapping)) {
                $mapping = $claimmapping[$key];
                $type = $mapping["type"] ?? "string";
                if ($mapping['isarray']) {
                    $value = explode(',', $value);
                    sort($value);
                } else if ($type == 'boolean') {
                    $value = isset($value) && ($value == 'true');
                }
                if (!empty($mapping['suffix'])) {
                    $claim .= "-{$mapping['suffix']}";
                }
                $claim .= '/claim/';
                if (is_null($mapping['group'])) {
                    $payload[$mapping['claim']] = $value;
                } else if (empty($mapping['group'])) {
                    $payload["{$claim}{$mapping['claim']}"] = $value;
                } else {
                    $claim .= $mapping['group'];
                    $payload[$claim][$mapping['claim']] = $value;
                }
            } else if (strpos($key, 'custom_') === 0) {
                $payload["{$claim}/claim/custom"][substr($key, 7)] = $value;
            } else if (strpos($key, 'ext_') === 0) {
                $payload["{$claim}/claim/ext"][substr($key, 4)] = $value;
            }
        }

        $privatekey = get_config('mod_lti', 'privatekey');
        $kid = get_config('mod_lti', 'kid');
        $jwt = JWT::encode($payload, $privatekey, 'RS256', $kid);

        $newparms = array();
        $newparms['id_token'] = $jwt;

        return $newparms;
    }
}