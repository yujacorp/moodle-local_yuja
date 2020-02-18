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
            LOCAL_YUJA_PLUGIN_NAME,
            get_string('pluginname', LOCAL_YUJA_PLUGIN_NAME)
        );

    // Heading.
    $setting = new admin_setting_heading(
            LOCAL_YUJA_PLUGIN_NAME . '/heading',
            '',
            get_string('setting_heading_desc', LOCAL_YUJA_PLUGIN_NAME)
        );
    $setting->plugin = LOCAL_YUJA_PLUGIN_NAME;
    $settings->add($setting);

    // Access url.
    $setting = new admin_setting_configtext(
            LOCAL_YUJA_PLUGIN_NAME . '/access_url',
            get_string('setting_access_url_label', LOCAL_YUJA_PLUGIN_NAME),
            get_string('setting_access_url_desc', LOCAL_YUJA_PLUGIN_NAME),
            '',
            PARAM_TEXT
        );
    $setting->plugin = LOCAL_YUJA_PLUGIN_NAME;
    $settings->add($setting);

    // Consumer_key.
    $setting = new admin_setting_configtext(
            LOCAL_YUJA_PLUGIN_NAME . '/consumer_key',
            get_string('setting_consumer_key_label', LOCAL_YUJA_PLUGIN_NAME),
            get_string('setting_consumer_key_desc', LOCAL_YUJA_PLUGIN_NAME),
            '',
            PARAM_TEXT
        );
    $setting->plugin = LOCAL_YUJA_PLUGIN_NAME;
    $settings->add($setting);

    // Shared_secret.
    $setting = new admin_setting_configtext(
            LOCAL_YUJA_PLUGIN_NAME . '/shared_secret',
            get_string('setting_shared_secret_label', LOCAL_YUJA_PLUGIN_NAME),
            get_string('setting_shared_secret_desc', LOCAL_YUJA_PLUGIN_NAME),
            '', PARAM_TEXT
        );
    $setting->plugin = LOCAL_YUJA_PLUGIN_NAME;
    $settings->add($setting);

    $ADMIN->add('localplugins', $settings);
}
