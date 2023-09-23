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
use local_wb_faq\shopping_cart;
use moodle_exception;
use moodle_url;
use stdClass;

/**
 * Dynamic optiondate form.
 * @copyright Wunderbyte GmbH <info@wunderbyte.at>
 * @package   local_wb_faq
 * @author Georg Maißer
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class supportmessage extends dynamic_form {

    /**
     * {@inheritdoc}
     * @see moodleform::definition()
     */
    public function definition() {

        global $USER;

        $mform = $this->_form;

        $group = $this->_ajaxformdata['groups'] ?? '';

        // Add priority select field.
        $priorities = [
            'low' => get_string('low', 'local_wb_faq'),
            'medium' => get_string('medium', 'local_wb_faq'),
            'high' => get_string('high', 'local_wb_faq'),
        ];

        $mform->addElement('select', 'priority', get_string('priority', 'your_plugin_name'), $priorities);

        // Get an array from the settings.
        $groupsnmodules = explode(PHP_EOL, get_config('groupsnmodules', 'local_faq'));
        $groups = [];
        $modules = [];

        foreach ($groupsnmodules as $line) {
            list($shortgroup, $namegroup, $shortmodule, $namemodule) = explode(',', $line);
            $groups[$shortgroup] = $namegroup;

            // We only add the modules for the selected group.
            if ($group == $shortgroup) {
                $modules[$shortmodule] = $namemodule;
            }
        }
        $mform->addElement('select', 'groups', get_string('groups', 'local_wb_faq'), $groups);

        $mform->addElement('select', 'nested_topic', get_string('modules', 'local_wb_faq'), $modules);
        $mform->disabledIf('nested_topic', 'topic', 'neq', '');

        // Button to attach JavaScript to to reload the form.
        $mform->registerNoSubmitButton('submitcattestoption');
        $mform->addElement('submit', 'submitmodulechoice', 'submitmodulechoice',
            ['class' => 'd-none', 'data-action' => 'submitmodulechoice']);

        // Add title field.
        $mform->addElement('text', 'title', get_string('title', 'your_plugin_name'));
        $mform->setType('title', PARAM_TEXT);

        // Add message textarea.
        $mform->addElement('textarea', 'message', get_string('message', 'your_plugin_name'));
        $mform->setType('message', PARAM_TEXT);

        $this->add_action_buttons(false, 'send message');

    }

    /**
     * Check access for dynamic submission.
     *
     * @return void
     */
    protected function check_access_for_dynamic_submission(): void {
        require_capability('local/wb_faq:cansendsupportmessage', $this->get_context_for_dynamic_submission());
    }

    /**
     * Process the form submission, used if form was submitted via AJAX
     *
     * This method can return scalar values or arrays that can be json-encoded, they will be passed to the caller JS.
     *
     * Submission data can be accessed as: $this->get_data()
     *
     * @return mixed
     */
    public function process_dynamic_submission() {

        global $USER;

        $data = $this->get_data();

        // Todo: do smth.

        return $data;
    }


    /**
     * Load in existing data as form defaults
     *
     * Can be overridden to retrieve existing values from db by entity id and also
     * to preprocess editor and filemanager elements
     *
     * Example:
     *     $this->set_data(get_entity($this->_ajaxformdata['cmid']));
     */
    public function set_data_for_dynamic_submission(): void {

        global $USER;
        $data = new stdClass();

        $this->set_data($data);
    }

    /**
     * Returns form context
     *
     * If context depends on the form data, it is available in $this->_ajaxformdata or
     * by calling $this->optional_param()
     *
     * @return context
     */
    protected function get_context_for_dynamic_submission(): context {

        return context_system::instance();
    }

    /**
     * Returns url to set in $PAGE->set_url() when form is being rendered or submitted via AJAX
     *
     * This is used in the form elements sensitive to the page url, such as Atto autosave in 'editor'
     *
     * If the form has arguments (such as 'id' of the element being edited), the URL should
     * also have respective argument.
     *
     * @return moodle_url
     */
    protected function get_page_url_for_dynamic_submission(): moodle_url {

        // We don't need it, as we only use it in modal.
        return new moodle_url('/');
    }

    /**
     * Validate form.
     *
     * @param stdClass $data
     * @param array $files
     * @return array
     */
    public function validation($data, $files) {

        $errors = array();

        return $errors;
    }

    /**
     * {@inheritDoc}
     * @see moodleform::get_data()
     */
    public function get_data() {
        $data = parent::get_data();
        return $data;
    }
}
