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
 * Moolde external API
 *
 * @package local_wb_faq
 * @category external
 * @copyright 2022 Wunderbyte Gmbh <info@wunderbyte.at>
 * @author Thomas Winkler
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/externallib.php');

use local_wb_faq\settings_manager;
use local_wb_faq\wb_faq;
class local_wb_faq_external extends external_api {

    /**
     * Delte an faq entry by id.
     *
     * @param int $id
     *
     * @return array
     */
    public static function delete_entry(int $id): array {
        global $DB;
        $params = external_api::validate_parameters(self::delete_entry_parameters(), [
            'id' => $id
        ]);

        $context = context_system::instance();

        if (!has_capability('local/wb_faq:canedit', $context)) {

            throw new moodle_exception('norighttoaccessthisfunction', 'local_wb_faq');

        } else {
            $array['status'] = settings_manager::delete_entry($params['id']);
        }

        return $array;
    }

    /**
     * Describes the paramters for add_item_to_cart.
     * @return external_function_parameters
     */
    public static function delete_entry_parameters() {
        return new external_function_parameters(array(
                'id'  => new external_value(PARAM_INT, 'id', VALUE_DEFAULT, ''),
            )
        );
    }

    /**
     * Describes the return values for add_item_to_cart.
     * @return external_single_structure
     */
    public static function delete_entry_returns() {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_BOOL, 'Status: true if success'),
            )
        );
    }
}
