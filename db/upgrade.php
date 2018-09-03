<?php

function xmldb_mymodule_upgrade($oldversion)
{
    global $CFG;

    $result = true;

// Insert PHP code from XMLDB Editor here
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
