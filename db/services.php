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
 * External functions of the plugin.
 *
 * @package    local_wb_faq
 * @copyright  2022 Wunderbyte GmbH <info@wunderbyte.at>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

defined('MOODLE_INTERNAL') || die();

$functions = [
    'local_wb_faq_get_category' => [
        'classname' => 'local_wb_faq\external\get_category',
        'description' => 'Returns a category with its subcategories and a page of its questions.',
        'type' => 'read',
        'ajax' => true,
        'loginrequired' => false,
    ],
    'local_wb_faq_get_entry' => [
        'classname' => 'local_wb_faq\external\get_entry',
        'description' => 'Returns the content of a question.',
        'type' => 'read',
        'ajax' => true,
        'loginrequired' => false,
    ],
    'local_wb_faq_search' => [
        'classname' => 'local_wb_faq\external\search',
        'description' => 'Searches categories and questions.',
        'type' => 'read',
        'ajax' => true,
        'loginrequired' => false,
    ],
    'local_wb_faq_delete_entry' => [
        'classname' => 'local_wb_faq\external\delete_entry',
        'description' => 'Deletes a category including its content, or a question.',
        'type' => 'write',
        'ajax' => true,
        'capabilities' => 'local/wb_faq:canedit',
    ],
    'local_wb_faq_toggle_entry_visibility' => [
        'classname' => 'local_wb_faq\external\toggle_entry_visibility',
        'description' => 'Toggles the visibility of a category or question.',
        'type' => 'write',
        'ajax' => true,
        'capabilities' => 'local/wb_faq:canedit',
    ],
    'local_wb_faq_set_wbfaq_order' => [
        'classname' => 'local_wb_faq\external\set_wbfaq_order',
        'description' => 'Applies a new order to categories or questions with the same parent.',
        'type' => 'write',
        'ajax' => true,
        'capabilities' => 'local/wb_faq:canedit',
    ],
];
