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

defined('MOODLE_INTERNAL') || die('Must access from moodle');

global $CFG;
require_once($CFG->dirroot. '/mod/lti/locallib.php');

/**
 * The YuJa Moodle Client
 *
 * Encapsulates extracting LTI information and creating signed urls to access YuJa resources
 */
class yuja_client
{
    /**
     * Get the base lti request params
     * @param object $course the course
     * @param guid $requestid a unique ID for the request for which the paramters are generated
     * @return array
     */
    public function get_lti_params($course, $requestid) {
        global $USER, $CFG;

        $instance = new stdClass();
        $instance->id = $requestid;

        // This building of organizationid is based on the lti_view function in /mod/lit/locallib.php.
        $orgid = parse_url($CFG->wwwroot)['host'];
        $basicparams = lti_build_standard_request($instance, $orgid, false);

        $usergiven = (isset($USER->firstname)) ? $USER->firstname : '';
        $userfamily = (isset($USER->lastname)) ? $USER->lastname : '';
        $userfull = (isset($USER->fullname)) ? $USER->fullname : '';
        $useremail = (isset($USER->email)) ? $USER->email : '';
        $useridnumber = (isset($USER->idnumber)) ? $USER->idnumber : '';
        $userusername = (isset($USER->username)) ? $USER->username : '';

        if ($CFG->version <= '2014111000') {
            $roles = lti_get_ims_role($USER, 0, $course->id);
        } else {
            // Moodle 2.8 (2014111000) adds support for specifying whether this is an LTI 2.0 launch.
            $roles = lti_get_ims_role($USER, 0, $course->id, false);
        }

        // This more unique instance guid was implemented in https://tracker.moodle.org/browse/MDL-67612
        // for version 3.9 (2020061500)
        $toolconsumerinstanceguid = md5(get_site_identifier());

        $returnurlparams = array('course' => $course->id,
            'instanceid' => $instance->id,
            'sesskey' => sesskey());
        $url = new \moodle_url('/mod/lti/return.php', $returnurlparams);
        $returnurl = $url->out(false);

        $customparams = array(
            'context_id' => $course->id,
            'context_label' => $course->shortname,
            'context_title' => $course->fullname,
            'ext_lms' => 'moodle-2',
            'lis_person_sourcedid' => $useridnumber,
            'custom_lis_person_sourcedid' => $userusername,
            'ext_user_username' => $userusername,
            'lis_person_name_family' => $userfamily,
            'lis_person_name_full' => $userfull,
            'lis_person_name_given' => $usergiven,
            'lis_person_contact_email_primary' => $useremail,
            'lti_message_type' => 'basic-lti-launch-request',
            'lti_version' => 'LTI-1p0',
            'roles' => $roles,
            'tool_consumer_info_product_family_code' => 'moodle',
            'tool_consumer_info_version' => (string)$CFG->version,
            'custom_tool_consumer_instance_guid' => $orgid,
            'tool_consumer_instance_guid' => $toolconsumerinstanceguid,
            'launch_presentation_return_url' => $returnurl,
            'user_id' => $USER->id,
            'custom_context_id' => $course->idnumber,
            'custom_context_term' => $course->startdate,
            'custom_plugin_info' => $this->get_plugin_info(), 
            'not_gradeable' => 'true'
        );

        $params = array_merge($basicparams, $customparams);

        return $params;
    }

    /**
     * Urlencode the query params values
     * @param string $params
     * @return array
     */
    public function get_query($params) {
        $encodedparams = '';
        foreach ($params as $k => $v) {
            $encodedparams .= "$k=" . urlencode($v) . "&";
        }
        return substr($encodedparams, 0, -1);
    }

    /**
     * Get the signed lti parameters using OAuth
     * @param string $endpoint
     * @param string $method
     * @param int $courseid
     * @param array $params
     * @return array
     */
    public function get_signed_lti_params($endpoint, $method='GET', $courseid=null, $params=array()) {

        global $DB;

        if (!$this->has_lti_config()) {
            throw new Exception(get_string('no_lti_config', 'local_yuja'));
        } else if (empty($courseid)) {
            throw new Exception(get_string('no_course_id', 'local_yuja'));
        }

        $course = $DB->get_record('course', array('id' => (int)$courseid), '*', MUST_EXIST);
        $key = get_config('local_yuja', 'consumer_key');
        $secret = get_config('local_yuja', 'shared_secret');
        $queryparams = $this->get_lti_params($course, 'yuja-media-chooser');

        return lti_sign_parameters(array_replace($queryparams, $params), $endpoint, $method, $key, $secret);
    }

    /**
     * Sign and return a url for the yuja videos request
     * @param string|int $courseid
     * @return string
     */
    public function get_signed_videos_url($courseid, $uniquelaunchid) {
        $url = $this->get_yuja_videos_url();
        return $url . '?' . $this->get_query(
            $this->get_signed_lti_params(
                $url, 'GET', $courseid, array('ext_content_return_types' => 'lti_api;moodle-media-chooser', 'unique_launch_id' => $uniquelaunchid))
        );
    }

    /**
     * Sign and return a url for the yuja javascript
     * @param string|int $courseid
     * @return string
     */
    public function get_signed_js_url($courseid, $uniquelaunchid) {
        $url = $this->get_yuja_videos_url();
        return $url . '?' . $this->get_query(
            $this->get_signed_lti_params(
                $url, 'GET', $courseid, array('ext_content_return_types' => 'lti_api;moodle-media-chooser-js', 'unique_launch_id' => $uniquelaunchid))
        );
    }

    /**
     * Get the custom atto/tinymce params
     * @return array
     */
    public function get_texteditor_params($plugintype) {
        global $COURSE, $USER;

        $params = array();

        try {
            $uniquelaunchid = uniqid($USER->id.'_'.$COURSE->id.'_', true);
            $params['ltiVersion'] = get_config('local_yuja', 'lti_version');
            if ($this->get_lti_version() == '1.1' && $this->has_lti_config() && isset($COURSE->id)) {
                $params['yujaVideosUrl'] = $this->get_signed_videos_url($COURSE->id, $uniquelaunchid);
                $params['yujaJsUrl'] = $this->get_signed_js_url($COURSE->id, $uniquelaunchid);
            } else if ($this->has_lti3_config() && isset($COURSE->id)) {
                $tool = $this->get_matching_lti_tool(get_config('local_yuja', 'tool_url'));
                $login_initiation_params = $this->lti_1p3_build_login_request($plugintype, $COURSE->id, $tool, $USER->id, $uniquelaunchid);
                $params['lti3LoginInitUrl'] = $tool->lti_initiatelogin . '?' . $this->get_query($login_initiation_params);
            }
        } catch (Exception $e) {
            $param['yujaError'] = $e->getMessage();
        }

        return $params;
    }

    /**
     * Get the moodle webroot
     * @return string
     */
    public function get_webroot() {
        global $CFG;
        return rtrim($CFG->wwwroot, '/');
    }

    /**
     * Get the url for the yuja videos request
     * @return string
     */
    private function get_yuja_videos_url() {
        return get_config('local_yuja', 'access_url');
    }

    /**
     * Get the lti 1.3 tool url
     * @return string
     */
    private function get_yuja_lti3_tool_url() {
        return get_config('local_yuja', 'tool_url');
    }

    /**
     * Whether the config is setup for lti
     * @return boolean
     */
    public function has_lti_config() {
        return (!empty(get_config('local_yuja', 'access_url')) &&
        !empty(get_config('local_yuja', 'consumer_key')) &&
        !empty(get_config('local_yuja', 'shared_secret')));
    }
    
    /**
     * Whether the config is setup for lti 1.3
     * @return boolean
     */
    public function has_lti3_config() {
        return !empty(get_config('local_yuja', 'tool_url'));
    }
    
    /**
     * LTI version
     * @return boolean
     */
    public function get_lti_version() {
        return get_config('local_yuja', 'lti_version');
    }

    /**
     * Get the yuja local plugin info
     * @return string
     */
    public function get_plugin_info() {
        return 'yuja-moodle-' . get_config('local_yuja', 'version');
    }

    /**
     * Get the matching lti tool
     */
    public static function get_matching_lti_tool($endpoint) {
        global $DB;

        $ltitools = $DB->get_records('lti_types');

        foreach ($ltitools as $tool) {
            if ($tool->baseurl === $endpoint) {
                return lti_get_type_type_config($tool->id);
            }
        }

        return null;
    }

    /**
     * Prepares an LTI 1.3 login request
     *
     * @param int            $courseid  Course ID
     * @param stdClass       $config    Tool type configuration
     * @param string         $messagetype   LTI message type
     * @param int            $foruserid Id of the user targeted by the launch
     * @param string         $title     Title of content item
     * @param string         $text      Description of content item
     * @return array Login request parameters
     */
    function lti_1p3_build_login_request($plugintype, $courseid, $config, $foruserid, $uniquelaunchid, $title='', $text='') {
        global $USER, $CFG, $SESSION;
        $ltihint = [];
        $endpoint = get_config('local_yuja', 'tool_url');
        if (!empty($config->lti_toolurl_ContentItemSelectionRequest)) {
            $endpoint = $config->lti_toolurl_ContentItemSelectionRequest;
        }
        $messagetype = 'ContentItemSelectionRequest';
        $launchid = "ltilaunch_$messagetype".rand();
        $SESSION->$launchid =
            "{$courseid},{$config->typeid},{$messagetype},{$foruserid}," . base64_encode($title) . ',' . base64_encode($text);

        $endpoint = trim($endpoint);
        $services = lti_get_services();
        foreach ($services as $service) {
            [$endpoint] = $service->override_endpoint($messagetype, $endpoint, '', $courseid);
        }

        $ltihint['launchid'] = $launchid;
        $ltihint['uniquelaunchid'] = $uniquelaunchid;
        // If SSL is forced make sure https is on the normal launch URL.
        if (isset($config->lti_forcessl) && ($config->lti_forcessl == '1')) {
            $endpoint = lti_ensure_url_is_https($endpoint);
        } else if (!strstr($endpoint, '://')) {
            $endpoint = 'http://' . $endpoint;
        }

        $params = array();
        $params['iss'] = $CFG->wwwroot;
        $params['target_link_uri'] = $endpoint;
        $params['login_hint'] = $USER->id;
        $params['lti_message_hint'] = json_encode($ltihint);
        $params['client_id'] = $config->lti_clientid;
        $params['lti_deployment_id'] = $config->typeid;
        $params['plugin_type'] = $plugintype;
        return $params;
    }

}
