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

        // Define table block_coursecollection to be created.
        $table = new xmldb_table('block_coursecollection');

        // Adding fields to table block_coursecollection.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);

        // Adding keys to table block_coursecollection.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for block_coursecollection.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Coursecollection savepoint reached.
        upgrade_block_savepoint(true, 2018090300, 'coursecollection');
    }

    return $result;
}
