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
 * This class contains a list of webservice functions related to the Shopping Cart Module by Wunderbyte.
 *
 * @package    local_wb_faq
 * @copyright  2022 Georg Maißer <info@wunderbyte.at>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace local_wb_faq\external;

use external_api;
use external_function_parameters;
use external_value;
use external_single_structure;
use external_multiple_structure;
use local_wb_faq\wb_faq;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');

class set_wbfaq_order extends external_api {

    /**
     * Describes the paramters for set_wbfaq_order.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'ids' => new external_multiple_structure(
                new external_value(PARAM_INT, 'The request ID', VALUE_REQUIRED)
            )
        ]);
    }

    /**
     * Webservice for reordering wb_faqs.
     *
     * @param array $ids
     *
     * @return array
     */
    public static function execute(array $ids): array {
        if (!has_capability('local/wb_faq:canedit', \context_system::instance())) {
            $error = ['status' => 0, 'error' => 'no capability'];
            return $error;
        }
        $params = self::validate_parameters(self::execute_parameters(), [
            'ids' => $ids,
        ]);
        $error = wb_faq::set_order($params['ids']);

        $error = ['status' => 1, 'error' => 'none'];
        return $error;
    }

    /*
     * Returns description of method result value.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure(array(
            'status' => new external_value(PARAM_INT, 'Status'),
            'error' => new external_value(PARAM_TEXT, 'Error')
            )
        );
    }
}
