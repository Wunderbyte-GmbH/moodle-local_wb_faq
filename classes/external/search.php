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
 * Webservice searching the FAQ.
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
use local_wb_faq\output\search_view;

/**
 * Returns the context of the template local_wb_faq/search_results.
 *
 * @package    local_wb_faq
 * @copyright  2026 Wunderbyte GmbH <info@wunderbyte.at>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class search extends external_api {
    /**
     * Describes the parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'query' => new external_value(PARAM_TEXT, 'Search term'),
            'rootid' => new external_value(PARAM_INT, 'Topmost category of the FAQ instance', VALUE_DEFAULT, 0),
            'page' => new external_value(PARAM_INT, 'Page, starting at 0', VALUE_DEFAULT, 0),
            'uid' => new external_value(PARAM_ALPHANUMEXT, 'Identifier of the FAQ instance', VALUE_DEFAULT, ''),
            'pageurl' => new external_value(PARAM_LOCALURL, 'Page the FAQ is displayed on', VALUE_DEFAULT, ''),
        ]);
    }

    /**
     * Searches the categories and questions visible to the user.
     *
     * @param string $query
     * @param int $rootid
     * @param int $page
     * @param string $uid
     * @param string $pageurl
     * @return array
     */
    public static function execute(string $query, int $rootid = 0, int $page = 0, string $uid = '', string $pageurl = ''): array {
        global $PAGE;

        $params = self::validate_parameters(self::execute_parameters(), [
            'query' => $query,
            'rootid' => $rootid,
            'page' => $page,
            'uid' => $uid,
            'pageurl' => $pageurl,
        ]);
        structures::validate_read_access();

        $view = new search_view(
            new access(),
            structures::page_urls($params['pageurl']),
            $params['uid'],
            $params['rootid'],
            $params['query'],
            $params['page']
        );

        return $view->export_for_template($PAGE->get_renderer('local_wb_faq'));
    }

    /**
     * Describes the result.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        $resultfields = structures::question_fields() + [
            'iscategory' => new external_value(PARAM_BOOL, 'Whether the result is a category'),
            'parenttitle' => new external_value(PARAM_RAW, 'Name of the parent category'),
            'parenturl' => new external_value(PARAM_URL, 'Link to the parent category'),
            'excerpt' => new external_value(PARAM_TEXT, 'Beginning of the content'),
        ];

        return new external_single_structure([
            'uid' => new external_value(PARAM_ALPHANUMEXT, 'Identifier of the FAQ instance'),
            'root' => new external_value(PARAM_INT, 'Topmost category of the FAQ instance'),
            'query' => new external_value(PARAM_TEXT, 'The search term'),
            'page' => new external_value(PARAM_INT, 'The page, starting at 0'),
            'total' => new external_value(PARAM_INT, 'Number of results'),
            'tooshort' => new external_value(PARAM_BOOL, 'Whether the search term was too short to be executed'),
            'minlength' => new external_value(PARAM_INT, 'Minimum length of a search term'),
            'hasresults' => new external_value(PARAM_BOOL, 'Whether there are results'),
            'results' => new external_multiple_structure(new external_single_structure($resultfields)),
            'pagination' => structures::pagination(),
        ]);
    }
}
