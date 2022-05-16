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
 * Module Wizard external functions and service definitions.
 *
 * @package local_wb_faq
 * @category external
 * @copyright 2022 Wunderbyte GmbH (info@wunderbyte.at)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 3.1
 */
// TODO Events.

 /*
defined('MOODLE_INTERNAL') || die();

$services = array(
        'Wunderbyte wb_faq external' => array(
                'functions' => array (
                        'local_wb_faq_copy_module'
                ),
                'restrictedusers' => 1,
                'shortname' => 'local_wb_faq_external',
                'enabled' => 1
        )
);

$functions = array(
        'local_wb_faq_copy_module' => array(
                'classname' => 'local_wb_faq_external',
                'methodname' => 'copy_module',
                'classpath' => 'local/wb_faq/classes/external.php',
                'description' => 'Copies a module to a new place',
                'type' => 'write',
                'ajax' => true,
                'capabilities' => 'local/wb_faq:copymodule',
                'services' => array(
                        'local_wb_faq_external'
                )
        ),
        'local_wb_faq_update_module' => array(
                'classname' => 'local_wb_faq_external',
                'methodname' => 'update_module',
                'classpath' => 'local/wb_faq/classes/external.php',
                'description' => 'Update a module with new information',
                'type' => 'write',
                'ajax' => true,
                'capabilities' => 'local/wb_faq:copymodule',
                'services' => array(
                        'local_wb_faq_external'
                )
        ),
        'local_wb_faq_delete_module' => array(
                'classname' => 'local_wb_faq_external',
                'methodname' => 'delete_module',
                'classpath' => 'local/wb_faq/classes/external.php',
                'description' => 'Deletes a module from a certain place.',
                'type' => 'write',
                'ajax' => true,
                'capabilities' => 'local/wb_faq:copymodule',
                'services' => array(
                        'local_wb_faq_external'
                )
        ),
        'local_wb_faq_create_course' => array(
                'classname' => 'local_wb_faq_external',
                'methodname' => 'create_course',
                'classpath' => 'local/wb_faq/classes/external.php',
                'description' => 'Copies a course by id.',
                'type' => 'write',
                'ajax' => true,
                'capabilities' => 'local/wb_faq:copymodule',
                'services' => array(
                        'local_wb_faq_external'
                )
        )
);
*/