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
 * Webservice returning the fragment of a category.
 *
 * @package    local_wb_faq
 * @copyright  2026 Wunderbyte GmbH <info@wunderbyte.at>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace local_wb_faq\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use local_wb_faq\local\access;
use local_wb_faq\output\category_view;

/**
 * Returns the context of the template local_wb_faq/faq.
 *
 * @package    local_wb_faq
 * @copyright  2026 Wunderbyte GmbH <info@wunderbyte.at>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_category extends external_api {
    /**
     * Describes the parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'categoryid' => new external_value(PARAM_INT, 'Category, 0 for the top level', VALUE_DEFAULT, 0),
            'rootid' => new external_value(PARAM_INT, 'Topmost category of the FAQ instance', VALUE_DEFAULT, 0),
            'entryid' => new external_value(PARAM_INT, 'Question to open, overrides category and page', VALUE_DEFAULT, 0),
            'page' => new external_value(PARAM_INT, 'Page, starting at 0', VALUE_DEFAULT, 0),
            'allowedit' => new external_value(PARAM_BOOL, 'Whether to include the edit controls', VALUE_DEFAULT, false),
            'uid' => new external_value(PARAM_ALPHANUMEXT, 'Identifier of the FAQ instance', VALUE_DEFAULT, ''),
            'pageurl' => new external_value(PARAM_LOCALURL, 'Page the FAQ is displayed on', VALUE_DEFAULT, ''),
        ]);
    }

    /**
     * Returns the fragment of a category.
     *
     * @param int $categoryid
     * @param int $rootid
     * @param int $entryid
     * @param int $page
     * @param bool $allowedit
     * @param string $uid
     * @param string $pageurl
     * @return array
     */
    public static function execute(
        int $categoryid = 0,
        int $rootid = 0,
        int $entryid = 0,
        int $page = 0,
        bool $allowedit = false,
        string $uid = '',
        string $pageurl = ''
    ): array {
        global $PAGE;

        $params = self::validate_parameters(self::execute_parameters(), [
            'categoryid' => $categoryid,
            'rootid' => $rootid,
            'entryid' => $entryid,
            'page' => $page,
            'allowedit' => $allowedit,
            'uid' => $uid,
            'pageurl' => $pageurl,
        ]);
        structures::validate_read_access();

        $view = new category_view(
            new access(),
            structures::page_urls($params['pageurl']),
            $params['uid'],
            $params['rootid'],
            $params['categoryid'],
            $params['entryid'],
            $params['page'],
            $params['allowedit']
        );

        return $view->export_for_template($PAGE->get_renderer('local_wb_faq'));
    }

    /**
     * Describes the result.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'uid' => new external_value(PARAM_ALPHANUMEXT, 'Identifier of the FAQ instance'),
            'root' => new external_value(PARAM_INT, 'Topmost category of the FAQ instance'),
            'categoryid' => new external_value(PARAM_INT, 'The category'),
            'entryid' => new external_value(PARAM_INT, 'The opened question'),
            'page' => new external_value(PARAM_INT, 'The page, starting at 0'),
            'title' => new external_value(PARAM_RAW, 'Name of the category'),
            'canedit' => new external_value(PARAM_BOOL, 'Whether edit controls are shown'),
            'isempty' => new external_value(PARAM_BOOL, 'Whether the category has no content'),
            'breadcrumbs' => new external_multiple_structure(new external_single_structure([
                'id' => new external_value(PARAM_INT, 'Category id'),
                'name' => new external_value(PARAM_RAW, 'Label'),
                'url' => new external_value(PARAM_URL, 'Deep link'),
                'active' => new external_value(PARAM_BOOL, 'Whether this is the current category'),
            ])),
            'categories' => new external_multiple_structure(new external_single_structure(structures::record_fields())),
            'entries' => new external_multiple_structure(new external_single_structure(structures::question_fields())),
            'pagination' => structures::pagination(),
        ]);
    }
}
