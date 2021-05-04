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
            get_string('pluginname', 'local_yuja')
        );

    // Heading.
    $setting = new admin_setting_heading(
            'local_yuja' . '/heading',
            '',
            get_string('setting_heading_desc', 'local_yuja')
        );
    $setting->plugin = 'local_yuja';
    $settings->add($setting);

    // Settings
    $setting_names = ['access_url', 'consumer_key', 'shared_secret'];
    for ($i = 0; $i < count($setting_names); $i++) {
        $setting_name = $setting_names[$i];
        $setting = new admin_setting_configtext(
            'local_yuja' . '/' . $setting_name,
            get_string('setting_' . $setting_name . '_label', 'local_yuja'),
            get_string('setting_' . $setting_name . '_desc', 'local_yuja'),
            '',
            PARAM_TEXT
        );
        $setting->plugin = 'local_yuja';
        $settings->add($setting);
    }

    $ADMIN->add('localplugins', $settings);
}
