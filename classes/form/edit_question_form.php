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
 * Form to create or edit a question.
 *
 * @package    local_wb_faq
 * @copyright  2022 Wunderbyte GmbH <info@wunderbyte.at>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace local_wb_faq\form;

use context_system;
use local_wb_faq\local\entry_repository;
use local_wb_faq\wb_faq;

/**
 * Form to create or edit a question.
 *
 * @package    local_wb_faq
 * @copyright  2022 Wunderbyte GmbH <info@wunderbyte.at>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class edit_question_form extends entry_form {
    /**
     * Form definition.
     *
     * @return void
     */
    public function definition(): void {
        $this->add_common_fields(entry_repository::TYPE_QUESTION, 'input:question');

        $this->_form->addElement(
            'editor',
            'content_editor',
            get_string('input:content', 'local_wb_faq'),
            null,
            wb_faq::get_textfield_options()
        );
        $this->_form->setType('content_editor', PARAM_RAW);
    }

    /**
     * Loads the record into the form.
     *
     * @return void
     */
    public function set_data_for_dynamic_submission(): void {
        $record = $this->get_record_for_form();
        $record->content = $record->content ?? '';
        $record->contentformat = FORMAT_HTML;

        $this->set_data(file_prepare_standard_editor(
            $record,
            'content',
            wb_faq::get_textfield_options(),
            context_system::instance(),
            'local_wb_faq',
            wb_faq::FILEAREA,
            $record->id ?: null
        ));
    }
}
