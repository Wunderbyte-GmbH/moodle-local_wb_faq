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


use MoodleQuickForm;
use local_wb_faq\settings_manager;
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

/*
global $DB;

$records = $DB->get_records_sql("SELECT * FROM {local_wb_faq_entry} faq ORDER BY parentid, type ASC LIMIT 40");
$categoryrecords = $DB->get_records_sql("SELECT * FROM {local_wb_faq_entry} WHERE type = '0' ORDER BY parentid, type ");

$catarr = array_values($categoryrecords);

function buildtree($elements, $parentid = 0, $depth = 0) {
    $branch = array();
    foreach ($elements as $element) {
        if ($element->parentid == $parentid) {
            $children = buildTree($elements, $element->id, $depth++);
            if ($children) {
                $element->children[] = $children;
            }
            $prefix = "";
            for ($i = 0; $i <= $depth; $i++) {
                $prefix .= "-";
            }
            $branch[] = $element;
        }
    }
    return $branch;
}

function buildchildren(&$array, $node, $delimiter) {
    foreach ($node->children as $row => $child) {
        foreach ($child as $row2 => $c) {
            $array[$c->id] = $delimiter."".$c->title;

            if ($c->children) {
                buildchildren($array, $c, $delimiter.'-');
            }
        }
    }
}

$tree = buildtree($catarr, 30239);
$option = [];
$nodes = $tree;

foreach ($nodes as $node) {
    $option[$node->id] = $node->title;
    if ($node->children) {
        buildchildren($option, $node, '-');
    }
}


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
*/

global $DB;
$records = $DB->get_records_sql("SELECT * FROM {local_wb_faq_entry} faq ORDER BY parentid, type ASC");



$test = new settings_manager();
$id = $test->get_id_from_categoryname('"test"');
$root = 0;
$d = $test->load_from_cache(true, $root);

$o = $test->buildselect($root);

$searchtree  = $test->buildsearchtree($root);
$recordsvalues = array_values($searchtree);
$b['json'] = json_encode($recordsvalues, true);

$data['json'] = $d;
$data['root'] = $root;
echo $OUTPUT->header();
$mform = new local_wb_faq\form\categories($o);
$mform->display();
echo $OUTPUT->render_from_template('local_wb_faq/search', $b);
echo $OUTPUT->render_from_template('local_wb_faq/js', $data);
echo $OUTPUT->footer();
