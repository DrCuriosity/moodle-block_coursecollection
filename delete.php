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

require_login();
$context = context_system::instance();
$PAGE->set_context($context);
$adminroot = admin_get_root(false, false); // Settings not required - only pages.

$collectionid = required_param('coursecollectionid', PARAM_INT);
$confirm = optional_param('confirm', 0, PARAM_INT);
$returnurl = optional_param('return', $CFG->wwwroot, PARAM_LOCALURL);
$confirmurl = new moodle_url('/blocks/coursecollection/delete.php', array(
    'coursecollectionid' => $collectionid,
    'confirm' => 1,
    'return' => $returnurl
));


if (confirm_sesskey()) {
    $rec = $DB->get_record('block_coursecollection_map', array('id' => $collectionid));

    if ($rec) {
        // Users should only be able to remove their own records.
        if ($USER->id == $rec->userid) {
            $course = $DB->get_record('course', array('id' => $rec->courseid));

            if (!$confirm) {
                $PAGE->set_url('/blocks/coursecollection/delete.php', array(
                    'coursecollectionid' => $collectionid,
                    'return' => $returnurl
                ));
                echo $OUTPUT->header();
                echo $OUTPUT->confirm(
                    get_string('deleterecordconfirm', 'block_coursecollection', $course),
                    $confirmurl,
                    $returnurl
                );
                echo $OUTPUT->footer();
            } else {
                $DB->delete_records('block_coursecollection_map', array('id' => $collectionid));
                redirect($returnurl, get_string('recorddeleted', 'block_coursecollection', $course));
            }
        } else {
            redirect($returnurl, get_string('deletewronguser', 'block_coursecollection'));
        }
    } else {
        // Record 'coursecollectionid' Not found.
        redirect($returnurl, get_string('deletenotfound', 'block_coursecollection'));
    }
}
