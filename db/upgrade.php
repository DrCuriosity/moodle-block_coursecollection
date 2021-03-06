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

defined('MOODLE_INTERNAL') || die();

function xmldb_mymodule_upgrade($oldversion) {
    global $CFG;

    $result = true;

    // PHP code from XMLDB Editor.
    if ($oldversion < 2018090300) {

        // Define table user_course_map to be created.
        $table = new xmldb_table('block_coursecollection_map');

        // Adding fields to table user_course_map.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table user_course_map.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for user_course_map.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Coursecollection savepoint reached.
        upgrade_block_savepoint(true, 2018090300, 'coursecollection');
    }

    return $result;
}
