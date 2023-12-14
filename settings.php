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
 * Settings for Yuja Local Plugin
 * @package    local_yuja
 * @subpackage yuja
 * @copyright  2016 Yuja
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die('Must access from moodle');

global $CFG;
require_once($CFG->dirroot. '/local/yuja/lib.php');

if ($hassiteconfig) {
    $settings = new admin_settingpage(
            'local_yuja',
            new lang_string('pluginname', 'local_yuja')
        );

    // Heading.
    $setting = new admin_setting_heading(
            'local_yuja' . '/heading',
            '',
            new lang_string('setting_heading_desc', 'local_yuja')
        );
    $setting->plugin = 'local_yuja';
    $settings->add($setting);

    // Select lti version
    $setting = new admin_setting_configselect(
           'local_yuja/lti_version',
           new lang_string('setting_lti_version_label', 'local_yuja'),
           new lang_string('setting_lti_version_desc', 'local_yuja'),
           1,
           array("1.1" => "LTI 1.1", "1.3" => "LTI 1.3")
       );
    $setting->plugin = 'local_yuja';
    $settings->add($setting);

    // Settings
    $settingnames = ['access_url', 'consumer_key', 'shared_secret'];
    for ($i = 0; $i < count($settingnames); $i++) {
        $settingname = $settingnames[$i];
        $setting = new admin_setting_configtext(
            'local_yuja' . '/' . $settingname,
            new lang_string('setting_' . $settingname . '_label', 'local_yuja'),
            new lang_string('setting_' . $settingname . '_desc', 'local_yuja'),
            '',
            PARAM_TEXT
        );
        $setting->plugin = 'local_yuja';
        $settings->hide_if('local_yuja' . '/' . $settingname, 'local_yuja/lti_version', 'eq', "1.3");
        $settings->add($setting);
    }
    
    $setting = new admin_setting_configtext(
        'local_yuja/tool_url',
        new lang_string('setting_tool_url_label', 'local_yuja'),
        new lang_string('setting_tool_url_desc', 'local_yuja'),
        '',
        PARAM_TEXT
    );
    $setting->plugin = 'local_yuja';
    $settings->hide_if('local_yuja/tool_url', 'local_yuja/lti_version', 'eq', "1.1");
    $settings->add($setting);


    $ADMIN->add('localplugins', $settings);
}
