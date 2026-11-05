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
 * Webservice returning the content of a question.
 *
 * @package    local_wb_faq
 * @copyright  2026 Wunderbyte GmbH <info@wunderbyte.at>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace local_wb_faq\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;
use local_wb_faq\local\access;
use local_wb_faq\output\record_exporter;

/**
 * Loads the answer when a question gets opened.
 *
 * @package    local_wb_faq
 * @copyright  2026 Wunderbyte GmbH <info@wunderbyte.at>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_entry extends external_api {
    /**
     * Describes the parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'id' => new external_value(PARAM_INT, 'Id of the question'),
            'rootid' => new external_value(PARAM_INT, 'Topmost category of the FAQ instance', VALUE_DEFAULT, 0),
        ]);
    }

    /**
     * Returns the content of a question.
     *
     * @param int $id
     * @param int $rootid
     * @return array
     */
    public static function execute(int $id, int $rootid = 0): array {
        $params = self::validate_parameters(self::execute_parameters(), ['id' => $id, 'rootid' => $rootid]);
        structures::validate_read_access();

        $entry = (new access())->require_question($params['id'], $params['rootid']);

        return ['id' => (int)$entry->id, 'content' => record_exporter::format_content($entry)];
    }

    /**
     * Describes the result.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'id' => new external_value(PARAM_INT, 'Id of the question'),
            'content' => new external_value(PARAM_RAW, 'Formatted content'),
        ]);
    }
}
