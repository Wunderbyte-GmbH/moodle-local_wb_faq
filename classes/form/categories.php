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
 * Moodec entities dynamic Form
 *
 * @package     local_entities
 * @author      Thomas Winkler
 * @copyright   2021 Wunderbyte
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


namespace local_wb_faq\form;

use context_system;
use moodleform;
use stdClass;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . '/formslib.php');
// require_once(dirname(__FILE__) . '/../lib.php');

/**
 * Class entities_form
 *
 * @copyright   2021 Wunderbyte
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class categories extends moodleform {


    /**
     * @var $callingentity
     */
    public $categories;

    /**
     * entities_edit_product_form constructor.
     * @param mixed $entity
     */
    public function __construct($categories = null) {
        if ($categories) {
            $this->categories = $categories;
        } else {
            $this->categories = [];
        }
        parent::__construct();
    }

    /**
     *
     * Set the entity data.
     *
     * @param mixed $defaults
     * @return mixed
     */
    public function set_data_for_dynamic_submission() {
        global $DB;
        if ($id = $this->optional_param('id', 0, PARAM_INT)) {
            $categories = $DB->get_record('local_entities', ['id' => $id]);
            $this->set_data($this->categories);
        }
    }

    /**
     * Get a list of all entities
     */
    public function definition() {
        $mform = $this->_form;
        // Entity DETAILS.
        $options = array(
            'multiple' => false,
            'noselectionstring' => get_string('root', 'local_wb_faq'),
            'placeholder' => get_string('searchcategory', 'local_wb_faq')
        );
        $mform->addElement('autocomplete', 'categorypicker', '', $this->categories, $options);
        $mform->disable_form_change_checker();
    }

    /**
     *
     * Validate the form
     *
     * @param mixed $data
     * @param mixed $files
     * @return mixed
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        return $errors;
    }




    /**
     *
     * Set the page data.
     *
     * @param mixed $defaults
     * @return mixed
     */
    public function set_data($defaults) {
        $context = context_system::instance();
        $draftideditor = file_get_submitted_draft_itemid('description');
        $defaults->description['text'] = file_prepare_draft_area($draftideditor, $context->id,
            'local_entities', 'description', 0, array('subdirs' => true), $defaults->description['text']);
        $defaults->description['itemid'] = $draftideditor;
        $defaults->description['format'] = FORMAT_HTML;

        $options = array('maxbytes' => 204800, 'maxfiles' => 1, 'accepted_types' => ['jpg, png']);
        $defaults->picture = file_prepare_standard_filemanager(
            $defaults,
            'image',
            $options,
            $context,
            'local_entities',
            'image',
            $defaults->id);

        return parent::set_data($defaults);
    }
}
