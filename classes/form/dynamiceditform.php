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

namespace local_wb_faq\form;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("$CFG->libdir/formslib.php");

use coding_exception;
use context;
use context_system;
use core_form\dynamic_form;
use html_writer;
use moodle_exception;
use moodle_url;
use stdClass;
use local_wb_faq\settings_manager;

/**
 * Add edit form.
 *
 * @copyright Wunderbyte GmbH <info@wunderbyte.at>
 * @author Thomas Winkler
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class dynamiceditform extends dynamic_form {

    /**
     * Get context for dynamic submission.
     * @return context
     */
    protected function get_context_for_dynamic_submission(): context {
        return context_system::instance();
    }

    /**
     * Check access for dynamic submission.
     * @return void
     */
    protected function check_access_for_dynamic_submission(): void {
        require_capability('moodle/site:config', context_system::instance());
    }


    /**
     * Set data for dynamic submission.
     * @return void
     */
    public function set_data_for_dynamic_submission(): void {
        global $DB;

        $data = new stdClass;

        if ($this->_ajaxformdata['id'] && $this->_ajaxformdata['id'] > 0) {
            $data = new stdClass;
            $data = $DB->get_record('local_wb_faq_entry', array('id' => $this->_ajaxformdata['id']));
            $content = $data->content;
            unset($data->content);
            $data->content['text'] = $content;
            $data->content['format'] = 1;
        } else {
            // No semesters found in DB.
            $data->type = $this->_ajaxformdata['type'];
        }

        $this->set_data($data);
    }

    /**
     * Process dynamic submission.
     * @return stdClass|null
     */
    public function process_dynamic_submission(): stdClass {
        global $DB;

        // This is the correct place to save and update semesters.
        $data = $this->get_data();

        $settingsmanager = new settings_manager();
        if ($data->id) {
            $settingsmanager->update_faq($data);
        } else {
            $settingsmanager->create_faq($data);
        }

        return $this->get_data();
    }

    /**
     * Form definition.
     * @return void
     */
    public function definition(): void {
        global $DB;

        $mform = $this->_form;

        $mform->addElement('html', '<div id="wb_faq_quickedit-form">');
        $mform->addElement('html', '<div class="container"><div class="row"><div class="col-md-6">');
        $mform->addElement('text', 'title', get_string('input:title', 'local_wb_faq'));

        $faqtype[0] = 'category';
        $faqtype[1] = 'question';
        $mform->addElement('hidden', 'type', get_string('input:type', 'local_wb_faq'));

        $mform->addElement('html', '</div><div class="col-md-6">');
        $mform->addElement('text', 'sortorder', get_string('input:sortorder', 'local_wb_faq'));
        $sql = 'SELECT id, title From {local_wb_faq_entry} WHERE type = 0';
        $parents = $DB->get_records_sql($sql);
        $selectinput = [];
        $selectinput[0] = 'top Level';
        foreach ($parents as $parent) {
            $selectinput[$parent->id] = $parent->title;
        }
        $mform->addElement('select', 'parentid', get_string('input:parentid', 'local_wb_faq'), $selectinput);
        $mform->addElement('html', '</div></div><div class="row"><div class="col-md-12">');
        if ($this->_ajaxformdata['type'] != 0) {
            $context = \context_system::instance();
            $editoroptions = array('maxfiles' => EDITOR_UNLIMITED_FILES, 'noclean' => true, 'context' => $context);
            $mform->addElement('editor', 'content', get_string('input:content', 'local_wb_faq'),
                '', $editoroptions);
            $mform->setType('content', PARAM_RAW);
        }
        $mform->addElement('hidden', 'id');

        $mform->addElement('html', '</div></div></div>');

        // Buttons.
        $this->add_action_buttons();
    }

    /**
     * Server-side form validation.
     * @param array $data
     * @param array $files
     * @return array $errors
     */
    public function validation($data, $files): array {
        $errors = [];

        $data['semesteridentifier'] = array_map('trim', $data['semesteridentifier']);
        $data['semestername'] = array_map('trim', $data['semestername']);

        $semesteridentifiercounts = array_count_values($data['semesteridentifier']);
        $semesternamecounts = array_count_values($data['semestername']);

        foreach ($data['semesteridentifier'] as $idx => $semesteridentifier) {
            if (empty($semesteridentifier)) {
                $errors["semesteridentifier[$idx]"] = get_string('erroremptysemesteridentifier', 'booking');
            }
            if ($semesteridentifiercounts[$semesteridentifier] > 1) {
                $errors["semesteridentifier[$idx]"] = get_string('errorduplicatesemesteridentifier', 'booking');
            }
        }

        foreach ($data['semestername'] as $idx => $semestername) {
            if (empty($semestername)) {
                $errors["semestername[$idx]"] = get_string('erroremptysemestername', 'booking');
            }
            if ($semesternamecounts[$semestername] > 1) {
                $errors["semestername[$idx]"] = get_string('errorduplicatesemestername', 'booking');
            }
        }

        foreach ($data['semesterstart'] as $idx => $semesterstart) {
            if ($semesterstart >= $data['semesterend'][$idx]) {
                $errors["semesterstart[$idx]"] = get_string('errorsemesterstart', 'booking');
                $errors["semesterend[$idx]"] = get_string('errorsemesterend', 'booking');
            }
        }

        return $errors;
    }

    /**
     * Get page URL for dynamic submission.
     * @return moodle_url
     */
    protected function get_page_url_for_dynamic_submission(): moodle_url {
        return new moodle_url('/local/wb_faq/admin.php');
    }
}
