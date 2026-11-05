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
 * Data generator of the plugin.
 *
 * @package    local_wb_faq
 * @copyright  2026 Wunderbyte GmbH <info@wunderbyte.at>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

use local_wb_faq\local\entry_repository;

/**
 * Creates categories and questions for tests.
 *
 * @package    local_wb_faq
 * @copyright  2026 Wunderbyte GmbH <info@wunderbyte.at>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_wb_faq_generator extends component_generator_base {
    /**
     * Creates a category.
     *
     * @param array $record
     * @return stdClass
     */
    public function create_category(array $record = []): stdClass {
        return $this->create_record($record + ['type' => entry_repository::TYPE_CATEGORY]);
    }

    /**
     * Creates a question.
     *
     * @param array $record
     * @return stdClass
     */
    public function create_question(array $record = []): stdClass {
        return $this->create_record($record + ['type' => entry_repository::TYPE_QUESTION, 'content' => '<p>Answer</p>']);
    }

    /**
     * Stores a record.
     *
     * @param array $record
     * @return stdClass
     */
    private function create_record(array $record): stdClass {
        static $counter = 0;
        $counter++;

        $record += [
            'title' => "Title $counter",
            'content' => '',
            'courseid' => 0,
            'enabled' => 1,
            'sortorder' => 0,
            'parentid' => 0,
            'timecreated' => time(),
            'timemodified' => time(),
        ];
        $id = (new entry_repository())->insert((object)$record);

        return (new entry_repository())->get($id);
    }
}
