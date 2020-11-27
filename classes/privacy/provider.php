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

namespace local_yuja\privacy;
use \core_privacy\local\metadata\collection;

// This plugin does store personal user data.
class provider implements \core_privacy\local\metadata\provider {

    public static function get_metadata(collection $collection) : collection {

        // Here you will add more items into the collection.

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