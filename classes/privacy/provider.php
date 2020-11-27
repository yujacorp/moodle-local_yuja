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

namespace local_yuja\privacy;
use \core_privacy\local\metadata\collection;

/**
 * The provider class for implementing the Privacy API for YuJa Moodle Client
 *
 * This documents which moodle data this plugin can access, and what it will be used for.
 * This plugin does store personal user data.
 */
class provider implements \core_privacy\local\metadata\provider {

    /**
     * Returns meta data about this system.
     * @param collection $collection the collection to add the information to
     * @return collection
     */
    public static function get_metadata(collection $collection) : collection {
        $collection->add_external_location_link('lti_client', [
            'user_id' => 'privacy:metadata:lti_client:user_id',
            'user_fullname' => 'privacy:metadata:lti_client:user_fullname',
            'course_id' => 'privacy:metadata:lti_client:course_id',
            'course_shortname' => 'privacy:metadata:lti_client:course_shortname',
            'course_fullname' => 'privacy:metadata:lti_client:course_fullname',
            'user_idnumber' => 'privacy:metadata:lti_client:user_idnumber',
            'user_username' => 'privacy:metadata:lti_client:user_username',
            'user_family' => 'privacy:metadata:lti_client:user_family',
            'user_given' => 'privacy:metadata:lti_client:user_given',
            'user_email' => 'privacy:metadata:lti_client:user_email',
            'roles' => 'privacy:metadata:lti_client:roles',
            'moodle_version' => 'privacy:metadata:lti_client:moodle_version',
            'course_idnumber' => 'privacy:metadata:lti_client:course_idnumber',
        ], 'privacy:metadata:lti_client');

        return $collection;
    }
}