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
 * Strings for Yuja Local Plugin
 * @package    local_yuja
 * @subpackage yuja
 * @copyright  2016 YuJa
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Plugin settings.
$string['pluginname'] = 'YuJa package config';

$string['setting_heading_desc'] = 'These settings customize the method in which your Moodle instance connects to your YuJa site.<br/><br/>Please see the following [support articles](http://support.yuja.com/) for complete information on this plugin.<br/><br/> It also may be necessary to purge your Moodle caches after changing these settings.<br/><br/>';

$string['setting_access_url_label'] = 'Your YuJa access URL:';
$string['setting_access_url_desc'] = '**Note:** This setting is your YuJa access URL provided by your YuJa Solutions Engineer.<br/><br/>';

$string['setting_consumer_key_label'] = 'Your YuJa consumer key';
$string['setting_consumer_key_desc'] = '**Note:** This is your unique YuJa LTI consumer key provided by your YuJa Solutions Engineer.';

$string['setting_shared_secret_label'] = 'Your YuJa shared secret';
$string['setting_shared_secret_desc'] = '**Note:** This is your unique YuJa LTI shared secret provided by your YuJa Solutions Engineer.';

$string['setting_lti_version_label'] = 'LTI Version';
$string['setting_lti_version_desc'] = '**Note:** Select which LTI version to enable.<br><br>';

$string['setting_tool_url_label'] = 'Tool URL';
$string['setting_tool_url_desc'] = '**Note:** The URL must match the Tool URL of the LTI 1.3 External tool.';

$string['no_course_id'] = 'Expected a valid course id';
$string['no_lti_config'] = 'LTI configuration settings are incomplete. Please update your YuJa package';


// Privacy API fields
$string['privacy:metadata:lti_client'] = 'In order to integrate with a remote LTI service, user and course data needs to be exchanged with that service.';
$string['privacy:metadata:lti_client:user_id'] = 'The userid is sent from Moodle to determine the correct user to provision into within the LTI Tool';
$string['privacy:metadata:lti_client:course_id'] = 'The course id is sent from Moodle to determine the correct course to provision into within the LTI Tool';
$string['privacy:metadata:lti_client:course_shortname'] = 'The course short name is sent from Moodle to determine the correct course to provision into within the LTI Tool, and for course creation within the LTI Tool';
$string['privacy:metadata:lti_client:course_fullname'] = 'The course full name is sent from Moodle to determine the correct course to provision into within the LTI Tool, and for course creation within the LTI Tool';
$string['privacy:metadata:lti_client:user_fullname'] = 'The user fullname is sent from Moodle to determine the correct user to provision into within the LTI Tool, and for user creation within the LTI Tool';
$string['privacy:metadata:lti_client:user_idnumber'] = 'The user idnumber is sent from Moodle to determine the correct user to provision into within the LTI Tool, and for user creation within the LTI Tool';
$string['privacy:metadata:lti_client:user_username'] = 'The user username is sent from Moodle to determine the correct user to provision into within the LTI Tool, and for user creation within the LTI Tool';
$string['privacy:metadata:lti_client:user_family'] = 'The user family name is sent from Moodle to determine the correct user to provision into within the LTI Tool, and for user creation within the LTI Tool';
$string['privacy:metadata:lti_client:user_given'] = 'The user given name is sent from Moodle to determine the correct user to provision into within the LTI Tool, and for user creation within the LTI Tool';
$string['privacy:metadata:lti_client:user_email'] = 'The user email is sent from Moodle to determine the correct user to provision into within the LTI Tool, and for user creation within the LTI Tool';
$string['privacy:metadata:lti_client:roles'] = 'The Moodle roles are sent from Moodle to determine the user\'s role within the LTI Tool, and for user creation within the LTI Tool';
$string['privacy:metadata:lti_client:moodle_version'] = 'The Moodle version number is sent from Moodle to determine the correct behaviour for the LTI Tool to take.';
$string['privacy:metadata:lti_client:course_idnumber'] = 'The course idnumber is sent from Moodle to determine the correct course to provision into within the LTI Tool, and for course creation within the LTI Tool';
$string['privacy:metadata:lti_client:course_startdate'] = 'The course startdate is sent from Moodle to determine the course term for when a new course is provisioned within the LTI Tool';
$string['privacy:metadata:lti_client:instance_guid'] = 'The instance guid is sent from Moodle to determine the correct users and courses to link to in the case that an organization has multiple moodle instances';