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
 * Behat data generator of the plugin.
 *
 * @package    local_wb_faq
 * @copyright  2026 Wunderbyte GmbH <info@wunderbyte.at>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

/**
 * Offers "the following "local_wb_faq > categories" exist" and the same for questions.
 *
 * @package    local_wb_faq
 * @copyright  2026 Wunderbyte GmbH <info@wunderbyte.at>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_local_wb_faq_generator extends behat_generator_base {
    /**
     * Returns the entities which can be created.
     *
     * @return array
     */
    protected function get_creatable_entities(): array {
        return [
            'categories' => [
                'singular' => 'category',
                'datagenerator' => 'category',
                'required' => ['title'],
                'switchids' => ['parent' => 'parentid', 'course' => 'courseid'],
            ],
            'questions' => [
                'singular' => 'question',
                'datagenerator' => 'question',
                'required' => ['title'],
                'switchids' => ['parent' => 'parentid'],
            ],
        ];
    }

    /**
     * Finds a category by its title.
     *
     * @param string $title
     * @return int
     */
    protected function get_parent_id(string $title): int {
        global $DB;

        return (int)$DB->get_field('local_wb_faq_entry', 'id', ['title' => $title, 'type' => '0'], MUST_EXIST);
    }
}
