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
 * @package    local_wb_faq
 * @copyright  2022 Wunderbyte GmbH
 * @author     Thomas Winkler
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */



require_once('../../config.php');


$delid = optional_param('del', 0, PARAM_INT);
$context = \context_system::instance();
$PAGE->set_context($context);
require_login();



$PAGE->set_url(new moodle_url('/local/wb_faq/wb_faq.php', array()));

$title = "FAQ";
$PAGE->set_title($title);
$PAGE->set_heading($title);


$json2 =
'{
  "1" : {
  "name": "Haben Sie Probleme mit dem Internet?",
  "id": "1",
  "parentid": "0",
  "timecreated": "123123",
  "toplevel": true,
  "entries": [
    {
      "id": "1",
      "name": "Haben Sie versucht es ein und auszuschalten",
      "content": "asdkasdlkajdsakljdlajsdl"
    },
    {
      "id": "2",
      "name": "Funktioniert eine andere Website",
      "content": "testentry2content"
    }
  ],
  "categories": [
    {
      "name": "Benützen Sie WLAN?",
      "parentid": "1",
      "id": "2"
    },
    {
      "name": "Benützen Sie Kabelgebundenes internet",
      "parentid": "1",
      "id": "3"
    },
    {
      "name": "Benützen Sie WLAN?",
      "parentid": "1",
      "id": "2"
    },
    {
      "name": "Benützen Sie WLAN?",
      "parentid": "1",
      "id": "2"
    },
    {
      "name": "Benützen Sie WLAN?",
      "parentid": "1",
      "id": "2"
    },
    {
      "name": "Benützen Sie WLAN?",
      "parentid": "1",
      "id": "2"
    },
    {
      "name": "Benützen Sie WLAN?",
      "parentid": "1",
      "id": "2"
    }
  ]
},
"2" :{
  "name": "Benützen Sie WLAN?",
  "id": "2",
  "parentid": "1",
  "timecreated": "123123",
  "allowcontact": true,
  "entries": [
    {
      "id": "3",
      "name": "asdasd Sie versucht es ein und auszuschalten",
      "content": "asdkasdlkajdsakljdlajsdl"
    },
    {
      "id": "4",
      "name": "asdasd eine andere Website",
      "content": "testentry2content"
    }
  ],
  "categories": [
  ]
},
"3" :{
  "name": "Benützen Sie Kabelgebundenes internet",
  "id": "3",
  "parentid": "1",
  "timecreated": "123123",
  "entries": [
    {
      "id": "4",
      "name": "asdasd Sie versucht es ein und auszuschalten",
      "content": "asdkasdlkajdsakljdlajsdl"
    },
    {
      "id": "5",
      "name": "asdasd Sie versucht es ein und auszuschalten",
      "content": "asdkasdlkajdsakljdlajsdl"
    },
    {
      "id": "6",
      "name": "asdasd Sie versucht es ein und auszuschalten",
      "content": "asdkasdlkajdsakljdlajsdl"
    },
    {
      "id": "7",
      "name": "asdasd Sie versucht es ein und auszuschalten",
      "content": "asdkasdlkajdsakljdlajsdl"
    },
    {
      "id": "8",
      "name": "asdasd Sie versucht es ein und auszuschalten",
      "content": "asdkasdlkajdsakljdlajsdl"
    },
    {
      "id": "9",
      "name": "asdasd Sie versucht es ein und auszuschalten",
      "content": "asdkasdlkajdsakljdlajsdl"
    },
    {
      "id": "13",
      "name": "asdasd Sie versucht es ein und auszuschalten",
      "content": "asdkasdlkajdsakljdlajsdl"
    },
    {
      "id": "15",
      "name": "asdasd Sie versucht es ein und auszuschalten",
      "content": "asdkasdlkajdsakljdlajsdl"
    },
    {
      "id": "163",
      "name": "asdasd Sie versucht es ein und auszuschalten",
      "content": "asdkasdlkajdsakljdlajsdl"
    },
    {
      "id": "1233",
      "name": "asdasd Sie versucht es ein und auszuschalten",
      "content": "asdkasdlkajdsakljdlajsdl"
    },
    {
      "id": "193",
      "name": "asdasd Sie versucht es ein und auszuschalten",
      "content": "asdkasdlkajdsakljdlajsdl"
    },
    {
      "id": "153",
      "name": "asdasd Sie versucht es ein und auszuschalten",
      "content": "asdkasdlkajdsakljdlajsdl"
    },
    {
      "id": "173",
      "name": "asdasd Sie versucht es ein und auszuschalten",
      "content": "asdkasdlkajdsakljdlajsdl"
    },
    {
      "id": "3333",
      "name": "asdasd Sie versucht es ein und auszuschalten",
      "content": "asdkasdlkajdsakljdlajsdl"
    },
    {
      "id": "333",
      "name": "asdasd Sie versucht es ein und auszuschalten",
      "content": "asdkasdlkajdsakljdlajsdl"
    },
    {
      "id": "33",
      "name": "asdasd Sie versucht es ein und auszuschalten",
      "content": "asdkasdlkajdsakljdlajsdl"
    },
    {
      "id": "31",
      "name": "asdasd Sie versucht es ein und auszuschalten",
      "content": "asdkasdlkajdsakljdlajsdl"
    },
    {
      "id": "32",
      "name": "asdasd Sie versucht es ein und auszuschalten",
      "content": "asdkasdlkajdsakljdlajsdl"
    },
    {
      "id": "34",
      "name": "asdasd Sie versucht es ein und auszuschalten",
      "content": "asdkasdlkajdsakljdlajsdl"
    },
    {
      "id": "35",
      "name": "asdasd Sie versucht es ein und auszuschalten",
      "content": "asdkasdlkajdsakljdlajsdl"
    },
    {
      "id": "36",
      "name": "asdasd Sie versucht es ein und auszuschalten",
      "content": "asdkasdlkajdsakljdlajsdl"
    },
    {
      "id": "37",
      "name": "asdasd Sie versucht es ein und auszuschalten",
      "content": "asdkasdlkajdsakljdlajsdl"
    },
    {
      "id": "48",
      "name": "asdasd eine andere Website",
      "content": "testentry2content"
    }
  ],
  "categories": [
  ]
}
}';

//$data['json'] = $json2;

// loading data for entries
global $DB;

$records = $DB->get_records_sql("SELECT * FROM {local_wb_faq_entry} faq ORDER BY parentid, type ASC");
$recordsvalues = array_values($records);
$dataarr = [];
$i = 1;
foreach ($recordsvalues as $record) {
  if ($record->type == 0) {
    $dataarr[$record->id] = $record;
    $dataarr[$record->parentid]->categories[] = $record;
    if ($record->parentid == 0) {
      $dataarr[$record->parentid]->title = "";
      $dataarr[$record->parentid]->toplevel = true;
    }
  }
  if ($record->type == 1) {
    $dataarr[$record->parentid]->entries[] = $record;
  }
}
$data['json'] = json_encode($dataarr, true);


echo $OUTPUT->header();

echo $OUTPUT->render_from_template('local_wb_faq/js', $data);
echo $OUTPUT->footer();
