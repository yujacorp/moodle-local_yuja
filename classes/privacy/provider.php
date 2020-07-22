<?php
// …
 
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