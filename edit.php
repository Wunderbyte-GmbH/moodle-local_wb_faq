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
use local_wb_faq\form\edit_form;
use core_customfield\api;

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

$settingsmanager = new \local_wb_faq\wb_faq();

if ($entityid) {
    $data = \local_wb_faq\wb_faq::get_settings_forform($entityid);
    $mform = new edit_form($data);
    $handler = local_wb_faq\customfield\wb_faq_handler::create();
    $handler->instance_form_before_set_data($data);
    $mform->set_data($data);
} else {
    $mform = new edit_form();
}

// Print the page header.
$title = isset($data) ? $data->name : get_string('new_entity', 'local_wb_faq');
$heading = isset($data->id) ? $data->name : get_string('new_entity', 'local_wb_faq');
if ($mform->is_cancelled()) {
    redirect(new moodle_url($CFG->wwwroot . '/local/wb_faq/wb_faq.php'));
} else if ($data = $mform->get_data()) {
    require_once($CFG->libdir . '/formslib.php');
    $context = context_system::instance();
    $data->description['text'] = file_save_draft_area_files($data->description['itemid'], $context->id,
        'local_wb_faq', 'entitycontent',
        0, array('subdirs' => true), $data->description['text']);

    $data->entitydata = '';
    $recordentity = new stdClass();
    $recordentity = $data;
    $recordentity->id = $data->id;
    $recordentity->name = $data->name;
    $recordentity->sortorder = intval($data->sortorder);
    $recordentity->type = $data->type;
    $recordentity->parentid = intval($data->parentid);
    $recordentity->description = $data->description['text'];
    $result = $settingsmanager->update_or_createentity($recordentity);
    if ($result && $result > 0) {
        $options = array('subdirs' => 0, 'maxbytes' => 204800, 'maxfiles' => 1, 'accepted_types' => '*');
        if (isset($data->image_filemanager)) {
            file_postupdate_standard_filemanager($data, 'image', $options, $context, 'local_wb_faq', 'image', $result);
        }
        redirect(new moodle_url($CFG->wwwroot . '/local/wb_faq/wb_faq.php', array()));
    }
}
$PAGE->set_title($title);
$PAGE->set_heading($heading);
echo $OUTPUT->header();
printf('<h1 class="page__title">%s<a style="float:right;font-size:15px" href="' .
    new moodle_url($CFG->wwwroot . '/local/wb_faq/wb_faq.php') . '"> '.
    get_string('backtolist', 'local_wb_faq') .'</a></h1>',
    $title);

$mform->display();
$PAGE->requires->js_call_amd('local_wb_faq/customfield', 'init');

echo $OUTPUT->footer();
