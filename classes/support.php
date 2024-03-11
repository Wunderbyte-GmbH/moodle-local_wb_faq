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
 * local wb_faq
 *
 * @package     local_wb_faq
 * @author      Georg Maißer
 * @copyright   2024 Wunderbyte GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_wb_faq;

use stdClass;

/**
 * Class jwt
 * @author      Georg Maißer
 * @copyright   2024 Wunderbyte GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class support {

    /**
     *
     * @return void
     */
    public static function send_ticket(stdClass $data) {

        global $USER;

        $jwt = new jwt();

        $data->userName = $USER->username;

        // We also need the contactid and the account it.
        list($contactid, $accountid) = explode('-', $USER->idnumber);

        $data->contactId = $contactid ?? 0;
        $data->accountId = $accountid ?? 0;
        $data->problemText = '';
        $data->action = get_string('supportactioncreate', 'local_wb_faq');

        $token = $jwt->return_token((array)$data);

        $data->baseurl = get_config('local_wb_faq', 'supportmessagebaseurl');
        $data->token = $token;

    }

    public static function see_all_tickets() {

    }
}
