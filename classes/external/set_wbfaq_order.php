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
 * Webservice reordering siblings.
 *
 * @package    local_wb_faq
 * @copyright  2022 Georg Maißer <info@wunderbyte.at>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace local_wb_faq\external;

use context_system;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use local_wb_faq\wb_faq;

/**
 * Applies a new order to categories or questions with the same parent.
 *
 * @package    local_wb_faq
 * @copyright  2022 Georg Maißer <info@wunderbyte.at>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class set_wbfaq_order extends external_api {
    /**
     * Describes the parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'ids' => new external_multiple_structure(new external_value(PARAM_INT, 'Id of a category or question')),
        ]);
    }

    /**
     * Applies the order.
     *
     * @param array $ids
     * @return array
     */
    public static function execute(array $ids): array {
        $params = self::validate_parameters(self::execute_parameters(), ['ids' => $ids]);
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('local/wb_faq:canedit', $context);

        wb_faq::set_order($params['ids']);

        return ['status' => true];
    }

    /**
     * Describes the result.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'status' => new external_value(PARAM_BOOL, 'Whether the order was applied'),
        ]);
    }
}
