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

use context;
use context_system;
use core_form\dynamic_form;
use moodle_url;
use stdClass;
use local_wb_faq\wb_faq;

/**
 * Form to edit questions.
 *
 * @copyright Wunderbyte GmbH <info@wunderbyte.at>
 * @author Thomas Winkler
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class editCategoriesForm extends dynamic_form {

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
        require_capability('local/wb_faq:canedit', context_system::instance());
    }

    /**
     * Set data for dynamic submission.
     * @return void
     */
    public function set_data_for_dynamic_submission(): void {
        global $DB;

        $data = new stdClass();

        if ($this->_ajaxformdata['id'] && $this->_ajaxformdata['id'] > 0) {
            $data = new stdClass();
            $data = $DB->get_record('local_wb_faq_entry', array('id' => $this->_ajaxformdata['id']));
            $content = $data->content;
            unset($data->content);
            $data->content['text'] = $content;
            $data->content['format'] = 1;
        } else {
            $data->parentid = $this->_ajaxformdata['parentid'];
            $data->type = $this->_ajaxformdata['type'];
        }

        if ($group = $this->_ajaxformdata['supplement'] ?? null) {
            $data->supplement = $group;
        }

        $this->set_data($data);
    }

    /**
     * Process dynamic submission.
     * @return stdClass|null
     */
    public function process_dynamic_submission(): stdClass {
        // This is the correct place to save and update semesters.
        $data = $this->get_data();

        $settingsmanager = new wb_faq();
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
        $mform->addRule('title', get_string('required'), 'required');

        $mform->addElement('advcheckbox', 'enabled', get_string('invisible', 'local_wb_faq'));
        $mform->setDefault('enabled', 1);

        // Get a list of all courses.
        $sql = 'SELECT id, fullname From {course}';
        $courses = $DB->get_records_sql($sql);

        $coursesarray[0] = get_string('nocourseselected', 'local_wb_faq');

        foreach ($courses as $item) {
            $coursesarray[$item->id] = $item->fullname;
        }

        $options = array(
            'noselectionstring' => get_string('allareas', 'search'),
        );

        $mform->addElement('hidden', 'type', 0);
        $mform->addElement('hidden', 'nobuttons', 0);

        $mform->addElement('html', '</div><div class="col-md-6">');
        $sql = "SELECT id, title From {local_wb_faq_entry} WHERE type = '0'";
        $parents = $DB->get_records_sql($sql);
        $selectinput = [];
        $selectinput[0] = 'top Level';
        foreach ($parents as $parent) {
            $selectinput[$parent->id] = $parent->title;
        }
        $mform->addElement('select', 'parentid', get_string('input:parentid', 'local_wb_faq'), $selectinput);
        $mform->addElement('html', '</div></div><div class="row"><div class="col-md-12">');
        $mform->addElement('html', '<div class="form-group row" style="margin-bottom: 275px;">');  // Added margin-bottom
        $mform->addElement('html', '<label class="col-md-4 col-form-label">' . get_string('choosecourse', 'local_wb_faq') . '</label>');
        $mform->addElement('html', '<div class="col-md-8">');
        $mform->addElement('autocomplete', 'courseid', '', $coursesarray, $options);  // Leave label empty since it's handled above
        $mform->addElement('html', '</div></div>');

        $mform->addElement('hidden', 'id');

        $mform->addElement('html', '</div></div></div>');
    }

    public function definition_after_data() {

        $mform = $this->_form;
        $ajaxformdata = $mform->_defaultValues ?? $this->_ajaxformdata;

        wb_faq::add_form_elements($mform, $ajaxformdata);

        // We only show the action button on the admin page, else we likely use the modal which does not need them.
        $data = $this->_ajaxformdata;

        if (!isset($data['nobuttons'])) {
            $this->add_action_buttons();
        }
    }

    /**
     * Server-side form validation.
     * @param array $data
     * @param array $files
     * @return array $errors
     */
    public function validation($data, $files): array {
        $errors = [];

        if (empty($data['courseid'])) {
            $errors['courseid'] = get_string('nocourseid', 'local_wb_faq');
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
