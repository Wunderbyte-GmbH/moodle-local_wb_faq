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
 * Event observers used in forum.
 *
 * @package    local_wb_faq
 * @copyright  2024 Georg Maißer <info@wunderbyte.at>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_wb_faq;

/**
 * Event observer for local_wb_faq.
 */
class observer {

    /**
     * Triggered via payment_error event from any payment provider
     * If we receive a payment error, check for the order id in our shopping cart history.
     * And set it to error, if it was pending.
     *
     * @param \core\event\base $event
     * @return string
     */
    public static function user_loggedin(\core\event\base $event): string {

        global $USER;

        $data = $event->get_data();

        if (get_config('local_wb_faq', 'draweropen')) {
            set_user_preference('drawer-open-block', "1", $USER->id);
        }

        return 'registered_payment_error';
    }
}
