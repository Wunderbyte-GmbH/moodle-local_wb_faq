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
 * Form to create or edit a category.
 *
 * @package    local_wb_faq
 * @copyright  2022 Wunderbyte GmbH <info@wunderbyte.at>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace local_wb_faq\form;

use local_wb_faq\local\entry_repository;
use local_wb_faq\wb_faq;

/**
 * Form to create or edit a category.
 *
 * @package    local_wb_faq
 * @copyright  2022 Wunderbyte GmbH <info@wunderbyte.at>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class edit_category_form extends entry_form {
    /**
     * Form definition.
     *
     * @return void
     */
    public function definition(): void {
        global $DB;

        $this->add_common_fields(entry_repository::TYPE_CATEGORY, 'input:title');

        $courses = [0 => get_string('nocourseselected', 'local_wb_faq')];
        foreach ($DB->get_records('course', null, 'fullname ASC', 'id, fullname') as $course) {
            $courses[$course->id] = format_string($course->fullname);
        }
        $this->_form->addElement('autocomplete', 'courseid', get_string('choosecourse', 'local_wb_faq'), $courses);
        $this->_form->setType('courseid', PARAM_INT);
        $this->_form->setDefault('courseid', 0);
    }

    /**
     * Loads the record into the form.
     *
     * @return void
     */
    public function set_data_for_dynamic_submission(): void {
        $this->set_data($this->get_record_for_form());
    }

    /**
     * A category cannot be moved into itself or one of its own subcategories.
     *
     * @param array $data
     * @param array $files
     * @return array
     */
    public function validation($data, $files): array {
        $errors = parent::validation($data, $files);

        $id = (int)$data['id'];
        if ($id) {
            $subtree = wb_faq::get_category_subtree($id, (new entry_repository())->get_all_categories());
            if (in_array((int)$data['parentid'], $subtree, true)) {
                $errors['parentid'] = get_string('invalidparent', 'local_wb_faq');
            }
        }

        return $errors;
    }
}
