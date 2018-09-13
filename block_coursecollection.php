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
        global $PAGE;

        $this->title = get_string('pluginname', 'block_coursecollection');
        $PAGE->requires->js_call_amd('block_coursecollection/description-handler', 'init');
    }

    public function get_content() {
        global $CFG, $OUTPUT, $DB, $USER, $PAGE, $COURSE;

        $maxdisplaycount = 5; // TODO: Block config setting.

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

        if (! empty($this->config->text)) {
            $this->content->text .= $this->config->text;
        }

        $rows = $DB->get_records_sql(
            "SELECT cc.id, cc.courseid, shortname, fullname, summary"
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
        $enrolicon = $OUTPUT->pix_icon('t/add', get_string('enrolcoursecollection', 'block_coursecollection'));

        $collection = html_writer::start_tag('ul', array('id' => 'coursecollection'));

        $courseincollection = false; // Default unless we discovered it is.

        foreach ($rows as $record) {
            if ($currentcontext->contextlevel == CONTEXT_COURSE && $record->courseid == $COURSE->id) {
                $courseincollection = true;
            }

            /* TODO: Opportunistically delete any records where a user has already
             * enrolled? Or just filter against the appropriate table(s)?
             */

            // Build delete link.
            $deleteurl = new moodle_url(
                '/blocks/coursecollection/delete.php',
                array(
                    'coursecollectionid' => $record->id,
                    'sesskey' => sesskey(),
                    'return' => $PAGE->url
                )
            );
            $deletelink = html_writer::tag('a', $deleteicon, array('href' => $deleteurl));

            $enrolurl = new moodle_url(
                '/enrol/index.php',
                array(
                    'id' => $record->courseid,
                    'sesskey' => sesskey()
                )
            );
            $enrollink = html_writer::tag('a', $enrolicon, array('href' => $enrolurl));

            $item  = html_writer::start_tag('li');
            $item .= html_writer::start_tag('div', array('class' => 'name', 'title' => $record->shortname));
            $item .= $record->fullname;
            $item .= html_writer::start_tag('span', array('class' => 'actions'));
            $item .= $enrollink;
            $item .= $deletelink;
            $item .= html_writer::end_tag('span');
            $item .= html_writer::end_tag('div');
            $item .= html_writer::start_tag('div', array('class' => 'description'));
            $item .= $record->summary;
            $item .= html_writer::end_tag('div');
            $item .= html_writer::end_tag('li');

            $collection .= $item;
        }
        $collection .= html_writer::end_tag('ul');

        $this->content->text .= $collection;

        /* Is this is...
         * - A course page?
         * - But not the front page?
         * - A course that the current user is not already enrolled in?
         */
        $coursenotenrolledin = $currentcontext->contextlevel == CONTEXT_COURSE
            && $COURSE->id != SITEID
            && !is_enrolled($currentcontext, $USER);

        // If current course is not enrolled in, and not already in the collection...
        if ($coursenotenrolledin && !$courseincollection) {
            $addurl = new moodle_url(
                '/blocks/coursecollection/add.php',
                array(
                    'courseid' => $COURSE->id,
                    'sesskey' => sesskey(),
                    'return' => $PAGE->url
                )
            );
            $addlink = html_writer::tag('a',
                $enrolicon . get_string('addcoursecollection', 'block_coursecollection'),
                array('href' => $addurl, 'class' => 'addcoursecollection'));
            $this->content->text .= $addlink;
        }

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
}
