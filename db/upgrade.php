<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Plugin upgrade steps are defined here.
 *
 * @package     local_wb_faq
 * @category    upgrade
 * @copyright   2022 Wunderbyte Gmbh <info@wunderbyte.at>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Execute local_wb_faq upgrade from the given old version.
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_local_wb_faq_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    // For further information please read {@link https://docs.moodle.org/dev/Upgrade_API}.
    //
    // You will also have to create the db/install.xml file by using the XMLDB Editor.
    // Documentation for the XMLDB Editor can be found at {@link https://docs.moodle.org/dev/XMLDB_editor}.

    if ($oldversion < 2022090600) {

        // Define field courseid to be added to local_wb_faq_entry.
        $table = new xmldb_table('local_wb_faq_entry');
        $field = new xmldb_field('courseid', XMLDB_TYPE_INTEGER, '10', null, null, null, '0', 'content');

        // Conditionally launch add field courseid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Wb_faq savepoint reached.
        upgrade_plugin_savepoint(true, 2022090600, 'local', 'wb_faq');
    }

    if ($oldversion < 2022091700) {

        // Define field courseid to be added to local_wb_faq_entry.
        $table = new xmldb_table('local_wb_faq_entry');
        $field = new xmldb_field('enabled', XMLDB_TYPE_INTEGER, '1', null, null, null, '0', 'courseid');

        // Conditionally launch add field courseid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Set all enabled true.
        // As the field was present before and is null, we want to enable all previous record.
        $sql = "UPDATE {local_wb_faq_entry}
            SET enabled = 1";

        $DB->execute($sql);

        // Wb_faq savepoint reached.
        upgrade_plugin_savepoint(true, 2022091700, 'local', 'wb_faq');
    }

    if ($oldversion < 2023050800) {

        // Define field module to be added to local_wb_faq_entry.
        $table = new xmldb_table('local_wb_faq_entry');
        $field = new xmldb_field('module', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'parentid');

        // Conditionally launch add field module.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('supplement', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'module');

        // Conditionally launch add field group.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Wb_faq savepoint reached.
        upgrade_plugin_savepoint(true, 2023050800, 'local', 'wb_faq');
    }

    if ($oldversion < 2023092901) {

        // Define table local_wb_faq_issues to be created.
        $table = new xmldb_table('local_wb_faq_issues');

        // Adding fields to table local_wb_faq_issues.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('priority', XMLDB_TYPE_INTEGER, '1', null, null, null, null);
        $table->add_field('title', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('message', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('message_format', XMLDB_TYPE_INTEGER, '1', null, null, null, null);
        $table->add_field('groupname', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('module', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table local_wb_faq_issues.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Conditionally launch create table for local_wb_faq_issues.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Wb_faq savepoint reached.
        upgrade_plugin_savepoint(true, 2023092901, 'local', 'wb_faq');
    }

    if ($oldversion < 2023092902) {

        // Define field status to be added to local_wb_faq_issues.
        $table = new xmldb_table('local_wb_faq_issues');
        $field = new xmldb_field('status', XMLDB_TYPE_INTEGER, '1', null, null, null, '0', 'module');

        // Conditionally launch add field status.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Wb_faq savepoint reached.
        upgrade_plugin_savepoint(true, 2023092902, 'local', 'wb_faq');
    }

    if ($oldversion < 2023092905) {

        // Define field accountid to be added to local_wb_faq_issues.
        $table = new xmldb_table('local_wb_faq_issues');
        $field = new xmldb_field('accountid', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'module');

        // Conditionally launch add field accountid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Wb_faq savepoint reached.
        upgrade_plugin_savepoint(true, 2023092905, 'local', 'wb_faq');
    }

    return true;
}
