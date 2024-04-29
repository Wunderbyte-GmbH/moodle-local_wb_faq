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

use dml_exception;
use coding_exception;
use local_groupmanager\groupmanager;
use stdClass;

/**
 * Class jwt
 * @author      Georg Maißer
 * @copyright   2024 Wunderbyte GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class support {

    /**
     * Supportanfrage.
     * @param stdClass $data
     * @return object
     * @throws dml_exception
     * @throws coding_exception
     */
    public static function send_ticket(stdClass $data) {

        global $USER;

        $jwt = new jwt();

        $data->userName = $USER->username;

        // We also need the contactid and the account it.
        $clients = groupmanager::get_clients_for_user($USER);

        // We break when we have the right client.
        foreach ($clients as $client) {
            if ($client->identifier == $USER->institution) {
                break;
            }
        }
        $data->contactId = $client->contactid ?? 0;
        $data->accountId = $client->accountid ?? 0;
        $data->problemText = '';
        $data->action = $data->action ?? 'Anfrage';
        $data->sub = "Supportticket anlegen";

        $token = $jwt->return_token((array)$data);

        return (object)[
            'baseurl' => get_config('local_wb_faq', 'supportmessagebaseurl'),
            'token' => $token,
        ];

    }

    /**
     * See my tickets.
     * @param stdClass $data
     * @return object
     * @throws dml_exception
     */
    public static function see_all_tickets(stdClass $data) {
        global $USER;

        $jwt = new jwt();

        $data->userName = $USER->username;

        // We also need the contactid and the account it.
        $clients = groupmanager::get_clients_for_user($USER);

        // We break when we have the right client.
        foreach ($clients as $client) {
            if ($client->identifier == $USER->institution) {
                break;
            }
        }

        $data->contactId = $client->contactid ?? 0;
        $data->accountId = $client->accountid ?? 0;
        $data->sub = "Meine Supporttickets";
        $token = $jwt->return_token((array)$data);

        return (object)[
            'baseurl' => get_config('local_wb_faq', 'supportmessagebaseurl'),
            'token' => $token,
        ];
    }
}
