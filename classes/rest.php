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
use moodle_url;

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
     * @param stdClass $issue
     * @return stdClass
     */
    public static function send_issue(stdClass $issue) {

        $curl = curl_init();

        $url = get_config('local_wb_faq', 'resturl');

        $curlarray = self::return_curl_array_post($url);

        $curlarray[CURLOPT_POSTFIELDS] = json_encode(self::return_postfields_to_send_message($issue));

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
        }
        if (!empty($info["http_code"]) && $info["http_code"] === 500) {
            // This normally happens when the user is already present.
            // We check again if that's the case.
        }

        if (!empty($response)) {
            $response = json_decode($response, false);
        }
        return $response;
    }

    /**
     * Function to return fields to create or update user.
     * @param stdClass $issue
     * @return object
     */
    public static function return_postfields_to_send_message(stdClass $issue) {

        global $USER;

        $data = (object)[
            "ticketNr" => $issue->id,
            "externalIdent" => 'mdl-'.md5($issue->id . ' ' . $USER->id),
            "accountId" => $issue->accountid,
            "contactId" => $issue->clientid,
            "ticketText" => $issue->messsage,
            "createdDt" => date('Y-m-d\TH:i:s', $issue->timecreated),
            "lastModDt" => date('Y-m-d\TH:i:s', $issue->timecreated),
            "status" => "N",
            "gruppeCode" => $issue->groupname,
            "modulCode" => $issue->module,
            "kontaktTelNr" => null,
            "erreichbarInfo" => null,
            "participant" => null,
            "priority" => $issue->priority,
            "bearbeitungen" => [],
            "attachments" => [],
        ];

        // Now we add attachements, if there are any.
        self::add_attachment($data);

        return $data;
    }

    /**
     * @param stdClass $issue
     */
    private static function add_attachment(&$data) {

        global $USER;

        $context = context_system::instance();

        // If there is no token we can't transfer images.
        $token = get_config('local_wb_faq', 'imagetoken');

        $fs = get_file_storage();
        $files = $fs->get_area_files($context->id, 'local_wb_faq', 'supportmessages', $data->id);

        foreach ($files as $file) {

            $filename = $file->get_filename();

            // We can't deal with this.
            if (strlen($filename) < 2) {
                continue;
            }

            $downloadurl = new moodle_url('/webservice/rest/server.php', [
                'wsfunction' => 'local_wb_faq_provide_image',
                'wstoken' => $token ?? '',
                'moodlewsrestformat' => 'json',
                'postid' => $data->id,
                'filename' => $filename,
            ]);

            $attachment = [
                'externalIdent' => 'mdl-'.md5('attachment' . $data->id . $USER->id),
                'lastModDt' => date('Y-m-d\TH:i:s', $data->timecreated),
                'createdBy' => "<system>",
                'downloadLink' => $token ? $downloadurl->out(false) : 'novalidtokenavailable',
            ];

            $data['attachments'][] = $attachment;
        }

    }

    /**
     * @param string $url
     */
    public static function return_curl_array_post(string $url) {

        return [
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
              ),
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
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
