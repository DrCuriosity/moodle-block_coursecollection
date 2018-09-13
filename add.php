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
 * coursecollection block caps.
 *
 * @package    block_coursecollection
 * @copyright  David Thompson <david.thompson@catalyst.net.nz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');

require_once($CFG->libdir.'/adminlib.php');

global $DB;

require_login();
$context = context_system::instance();
$PAGE->set_context($context);
$adminroot = admin_get_root(false, false); // Settings not required - only pages.

$courseid = required_param('courseid', PARAM_INT);
$returnurl = optional_param('return', $CFG->wwwroot, PARAM_LOCALURL);

// Course collection maps courses to users.
$coursekeypair = array(
    'courseid' => $courseid,
    'userid' => $USER->id
);


if (confirm_sesskey()) {
    // Check course is not SITEID.
    if ($courseid == SITEID) {
        redirect($returnurl, get_string('addinvalidcourse', 'block_coursecollection', $courseid));
    }

    if (!$course = $DB->get_record('course', array('id' => $courseid))) {
        redirect($returnurl, get_string('addinvalidcourse', 'block_coursecollection', $courseid));
    }

    // Check user is not already enrolled to course.
    $coursecontext = context_course::instance($courseid);
    if (is_enrolled($coursecontext, $USER)) {
        redirect($returnurl, get_string('coursealreadyenrolled', 'block_coursecollection', $course));
    }

    // Check user/course pair is not already in the user's collection.
    if (!$courserecord = $DB->get_record('block_coursecollection_map', $coursekeypair)) {
        // Add user/course record.
        $DB->insert_record('block_coursecollection_map', $coursekeypair);
        redirect($returnurl, get_string('courseadded', 'block_coursecollection', $course));
        die();
    } else {
        redirect($returnurl, get_string('coursealreadyadded', 'block_coursecollection', $course));
    }
}
