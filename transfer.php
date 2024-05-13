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

use local_wb_faq\support;

require_once('../../config.php');

require_login();

$type = required_param('type', PARAM_TEXT);
$group = optional_param('group', '', PARAM_TEXT);
$module = optional_param('module', '', PARAM_TEXT);

// Here we create the jwt token.

$data = new stdClass();

$data->group = $group;
$data->module = $module;

$urlprefix = 'jwtaccess';

switch ($type) {
    case 'vertrieb':
        $data->action = 'Vertriebsanfrage';
        unset($data->group);
        unset($data->module);
        $data = support::send_ticket($data, );
        break;
    case 'ausbildung':
        unset($data->group);
        unset($data->module);
        $data->action = 'Ausbildung';
        $data = support::send_ticket($data);
        break;
    case 'stoerung':
        unset($data->group);
        unset($data->module);
        $data->action = 'Stoerung';
        $data = support::send_ticket($data);
        break;
    case 'mymessages':
        $data = support::see_all_tickets($data);
        break;
    default:
        throw new moodle_exception('notallowed', 'local_wb_faq');
}

if (get_config('local_wb_faq', 'debug')) {
    echo "$data->baseurl" . "$urlprefix?jwt=$data->token";
} else {
    // We just redirect to the baseurl & send the token.
    redirect("$data->baseurl" . "$urlprefix?jwt=$data->token");
}
