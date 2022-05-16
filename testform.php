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
 * local wb_faq
 *
 * @package     local_wb_faq
 * @author      Thomas Winkler
 * @copyright   2022 Wunderbyte GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot . '/local/wb_faq/lib.php');
use local_wb_faq\form\test_form;


$entityid = optional_param('id', 0, PARAM_INT);
$categoryid = optional_param('catid', 0, PARAM_INT);
$context = context_system::instance();

global $USER, $PAGE;

// Set PAGE variables.
$PAGE->set_context($context);
$PAGE->set_url($CFG->wwwroot . '/local/wb_faq/edit.php', array("id" => $entityid));

// Force the user to login/create an account to access this page.
require_login();

// Add chosen Javascript to list.
$PAGE->requires->jquery();

$PAGE->set_pagelayout('standard');

$a = class_exists("local_wb_faq\form\wb_faqrelation_form");

$b = class_exists("local_wb_faq\form\test_form");
$mform = new test_form();

// Print the page header.
$title = isset($data) ? $data->name : get_string('new_entity', 'local_wb_faq');
$heading = isset($data->id) ? $data->name : get_string('new_entity', 'local_wb_faq');

$PAGE->set_title($title);
$PAGE->set_heading($heading);

echo $OUTPUT->header();
printf('<h1 class="page__title">%s<a style="float:right;font-size:15px" href="' .
    new moodle_url($CFG->wwwroot . '/local/wb_faq/wb_faq.php') . '"> '.
    get_string('backtolist', 'local_wb_faq') .'</a></h1>',
    $title);


    $PAGE->requires->js_call_amd('local_wb_faq/dynamicform', 'init');

$mform->display();

echo $OUTPUT->footer();
