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
 * Entities Class to display list of entity records.
 * @package local_wb_faq
 * @author Georg Maißer
 * @copyright 2022 Wunderbyte GmbH
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_wb_faq;

use context_system;
use dml_exception;
use stdClass;

defined('MOODLE_INTERNAL') || die();

global $CFG;

/**
 * Class groupmanager
 *
 * @author Georg Maißer
 * @copyright 2022 Wunderbyte GmbH
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class rest {

    /**
     * groupmanager constructor.
     */
    public function __construct() {
    }

    /**
     * This feteches the array of attached persons for the client and then feteches...
     * ... contact details in a separate request.
     *
     * @param stdClass $user
     * @return stdClass
     */
    public static function send_issue(stdClass $user) {

        $curl = curl_init();

        $curlarray = self::return_curl_array_post('/rest/v2/users');

        $curlarray[CURLOPT_POSTFIELDS] = json_encode(self::return_postfields_to_create_user($user));

        curl_setopt_array($curl, $curlarray);

        $response = curl_exec($curl);

        if (!$response) {

            $error = curl_error($curl);
            $info = curl_getinfo($curl);

        }

        curl_close($curl);

        if (!empty($info["http_code"]) && $info["http_code"] === 409) {
            // This normally happens when the user is already present.
            // We check again if that's the case.
            $response = self::find_existing_rest_user($user);
        }
        if (!empty($info["http_code"]) && $info["http_code"] === 500) {
            // This normally happens when the user is already present.
            // We check again if that's the case.
            $response = self::find_existing_rest_user($user);
        }

        if (!empty($response)) {
            $response = json_decode($response, false);
        }

        self::check_success($response, $user, rest_ACTION_CREATE);

        return $response;
    }

    /**
     * Function to return fields to create or update user.
     * @param stdClass $data
     * @return object
     */
    public static function return_postfields_to_send_message(stdClass $data) {

        $data = (object)[
            "ticketNr" => 558660,
            "externalIdent" => $data->externaident,
            "accountId" => $data->accountid,
            "contactId" => $data->clientid,
            "ticketText" => $data->messsage,
            "createdDt" => "2023-05-04T14:56:57",
            "closedDt" => "2023-05-04T22:08:58",
            "lastModDt" => "2023-05-04T22:08:58",
            "status" => "N",
            "gruppeCode" => "PERS",
            "modulCode" => "Melde",
            "kontaktTelNr" => null,
            "erreichbarInfo" => null,
            "participant" => null,
            "priority" => null,
            "bearbeitungen" => [],
            "attachments" => [],
        ];
        return $data;
    }

    public static function return_curl_array_post(string $url) {

        $baseurl = get_config('local_wb_faq', 'restbaseurl');

        return [
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
              ),
            CURLOPT_URL => $baseurl . $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
        ];
    }

    public static function return_curl_array_get(string $url) {

        $baseurl = get_config('local_wb_faq', 'restbaseurl');

        return [
            CURLOPT_URL => $baseurl . $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ];
    }

    public static function return_curl_array_put(string $url) {

        $baseurl = get_config('local_wb_faq', 'restbaseurl');

        return [
            CURLOPT_URL => $baseurl . $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'PUT',
        ];
    }

    /**
     * This transform the response to std and checks for success (via id).
     * @param mixed $response
     * @param stdClass $user
     * @return bool
     * @throws dml_exception
     */
    private static function check_success($response, stdClass $user, $action = rest_ACTION_CREATE) {

        if (!empty($response->id)) {

            switch ($action) {
                case rest_ACTION_CREATE:
                    $event = rest_created::create(array(
                        'context' => context_system::instance(),
                        'other' => ['userid' => $user->id]));
                    $event->trigger();
                    return true;
                case rest_ACTION_UDATE:
                    $event = rest_updated::create(array(
                        'context' => context_system::instance(),
                        'other' => ['userid' => $user->id]));
                    $event->trigger();
                    return true;
                case rest_ACTION_FIND:
                default:
                    // We do nothing.
                    return true;
            }
        } else {
            $event = rest_failed::create(array(
                'context' => context_system::instance(),
                'other' => ['userid' => $user->id]));
            $event->trigger();
            return false;
        }
    }

    /**
     * Checks in DB if we have a rest ID, if so, don't continue.
     * The only checked param right now is if the user already has a rest id.
     * @param stdClass $user
     * @return string
     */
    private static function get_restid_of_user(stdClass $user) {

        global $DB;

        return $DB->get_field('local_wb_faq_udata', 'restid', ['userid' => $user->id]);
    }
}
