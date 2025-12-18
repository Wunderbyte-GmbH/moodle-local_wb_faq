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

$context = \context_system::instance();
$PAGE->set_context($context);
require_login();
$PAGE->set_url(new moodle_url('/local/wb_faq/filter.php', array()));

$title = "FAQ";
$PAGE->set_title($title);
$PAGE->set_heading($title);


$json = '{
    "category": [ {
       "name":"Sportart",
       "values":
          [
             {
                "key": "Basketball",
                "value" : "Basketball",
                "category" : "Sportart"
             },
             {
                "key" : "Schwimmen",
                "value": "Schwimmen",
                "category" : "Sportart"
             }
          ]
       },
    {
       "name":"Wochentag",
       "values":[
          {
             "key":"Montag",
             "value" : "Mo",
             "category": "Wochentag"
          },
          {
            "key":"Dienstag",
            "value" : "Di",
            "category": "Wochentag"
          }
       ]
    },
    {
        "name":"Test",
        "values":[
           {
              "key":"test",
              "value" : "bla",
              "category": "Test"
           },
           {
             "key":"test2",
             "value" : "bla2",
             "category": "Test"
           }
        ]
     },
     {
        "name":"Test2",
        "values":[
           {
              "key":"test",
              "value" : "bla2",
              "category": "Test2"
           },
           {
             "key":"test2",
             "value" : "bla22",
             "category": "Test2"
           }
        ]
     }
 ]
 }';

$data = json_decode($json, true);
echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_wb_faq/searchcontainer', $data);
echo $OUTPUT->footer();
