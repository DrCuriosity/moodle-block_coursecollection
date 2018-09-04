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

defined('MOODLE_INTERNAL') || die();

class block_coursecollection extends block_base {

    public function init() {
        $this->title = get_string('pluginname', 'block_coursecollection');
    }

    public function get_content() {
        global $CFG, $OUTPUT, $DB, $USER;
        $maxdisplaycount = 5;

        if ($this->content !== null) {
            return $this->content;
        }

        if (empty($this->instance)) {
            $this->content = '';
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';

        // The user/index.php expects course context, so get one if page has module context.
        $currentcontext = $this->page->context->get_course_context(false);

        if (! empty($this->config->text)) {
            $this->content->text = $this->config->text;
        }

        $this->content->text = '';
        if (empty($currentcontext)) {
            return $this->content;
        }
        if ($this->page->course->id == SITEID) {
            $this->content->text .= "site context";
        }

        if (! empty($this->config->text)) {
            $this->content->text .= $this->config->text;
        }

        $rows = $DB->get_records_sql(
            "SELECT cc.id, cc.courseid, shortname, fullname"
            . " FROM {course} c"
            . " JOIN {block_coursecollection_map} cc ON cc.courseid = c.id"
            . " WHERE cc.userid = :userid"
            . " ORDER BY shortname"
            . " LIMIT :max",
            array(
                'userid' => $USER->id,
                'max' => $maxdisplaycount
            )
        );

        // Prepare and build course collection table.
        $deleteicon = $OUTPUT->pix_icon('t/delete', get_string('removecoursecollection', 'block_coursecollection'));
        $subscribeicon = $OUTPUT->pix_icon('t/add', get_string('subscribecoursecollection', 'block_coursecollection'));

        $tbl = html_writer::start_tag('table', array('id' => 'coursecollection'));
        foreach ($rows as $record) {

            // Build delete link.
            $deleteurl = new moodle_url(
                '/blocks/coursecollection/delete.php',
                array(
                    'remove' => true,
                    'coursecollectionid' => $record->id,
                    'sesskey' => sesskey()
                )
            );
            $deletelink = html_writer::tag('a', $deleteicon, array('href' => $deleteurl));

            $subscribeurl = new moodle_url(
                '/...', // TODO: Find where this goes.
                array(
                    'subscribe' => true,
                    'courseid' => $record->courseid,
                    'sesskey' => sesskey()
                )
            );
            $subscribelink = html_writer::tag('a', $subscribeicon, array('href' => $subscribeurl));

            // Build table row.
            $row  = html_writer::start_tag('tr');
            $row .= html_writer::start_tag('td', array('class' => 'name', 'title' => $record->fullname));
            $row .= $record->shortname;
            $row .= html_writer::end_tag('td');
            $row .= html_writer::start_tag('td', array('class' => 'actions'));
            $row .= $subscribelink;
            $row .= $deletelink;
            $row .= html_writer::end_tag('td');
            $row .= html_writer::end_tag('tr');

            $tbl .= $row;
        }
        $tbl .= html_writer::end_tag('table');

        $this->content->text .= $tbl;

        return $this->content;
    }

    // My moodle can only have SITEID and it's redundant here, so take it away.
    public function applicable_formats() {
        return array('all' => false,
                     'site' => true,
                     'site-index' => true,
                     'course-view' => true,
                     'course-view-social' => false,
                     'mod' => true,
                     'mod-quiz' => false);
    }

    public function instance_allow_multiple() {
          return true;
    }

    public function has_config() {
        return true;
    }

    public function cron() {
            mtrace( "Hey, my cron script is running" );

                 // Do something.

                      return true;
    }
}
