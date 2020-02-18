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

/**
 * A class that encapsulated the YuJa Moodle Config values in config_plugins table as local_yuja
 */
class yuja_config
{
    /** @var string LTI consumer key */
    private $_consumer_key;
    /** @var string Yuja access URL */
    private $_access_url;
    /** @var string LTI consumer shared secret */
    private $_shared_secret;
    /** @var string Local plugin version */
    private $_version;
    /** @var string Moodle webroot */
    private $_webroot;
    /** @var array Stored members */
    private static $storedmembers = array(
        // These members are populated from the DB.
        '_consumer_key',
        '_access_url',
        '_shared_secret',
        '_version',
    );

    /**
     * Constructor
     */
    public function __construct() {
        global $CFG, $DB;

        $this->_webroot = $CFG->wwwroot;

        $records = $DB->get_records('config_plugins',
            array('plugin' => LOCAL_YUJA_PLUGIN_NAME));

        if (!empty($records)) {
            foreach ($records as $r) {
                $membername = '_' . $r->name;

                if (in_array($membername, self::$storedmembers)) {
                    $value = $r->value;

                    if (!empty($value)) {
                        $this->{$membername} = $value;
                    }
                }
            }
        }
    }

    /**
     * Whether lti is configured
     * @return boolean
     */
    public function has_lti_config() {
        return (!empty($this->_access_url) &&
                !empty($this->_consumer_key) &&
                !empty($this->_shared_secret));
    }

    /**
     * Get the yuja local plugin version
     * @return string
     */
    public function get_version() {
        return $this->_version;
    }

    /**
     * Get the yuja access_url
     * @return string
     */
    public function get_access_url() {
        return $this->_access_url;
    }

    /**
     * Get the lti consumer key
     * @return string
     */
    public function get_consumer_key() {
        return $this->_consumer_key;
    }

    /**
     * Get the lti consumer shared secret
     * @return string
     */
    public function get_shared_secret() {
        return $this->_shared_secret;
    }

    /**
     * Get the moodle webroot
     * @return string
     */
    public function get_webroot() {
        return rtrim($this->_webroot, '/');
    }

    /**
     * Get the yuja local plugin info
     * @return string
     */
    public function get_plugin_info() {
        return 'yuja-moodle-' . $this->get_version();
    }

}
