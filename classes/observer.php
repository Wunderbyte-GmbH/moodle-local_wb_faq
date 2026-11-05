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
 * Event observer of the plugin.
 *
 * @package    local_wb_faq
 * @copyright  2022 Wunderbyte GmbH <info@wunderbyte.at>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace local_wb_faq;

use core\event\user_loggedin;

/**
 * Event observer of the plugin.
 *
 * @package    local_wb_faq
 * @copyright  2022 Wunderbyte GmbH <info@wunderbyte.at>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class observer {
    /**
     * Opens the block drawer after a login, if this is activated in the settings.
     *
     * @param user_loggedin $event
     * @return void
     */
    public static function user_loggedin(user_loggedin $event): void {
        if (get_config('local_wb_faq', 'draweropen')) {
            set_user_preference('drawer-open-block', '1', $event->userid);
        }
    }
}
