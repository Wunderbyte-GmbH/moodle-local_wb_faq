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


$test = new settings_manager();
$id = $test->get_id_from_categoryname('"test"');
$root = 0;
$d = $test->load_from_cache(true, $root);

$o = $test->buildselect($root);

$searchtree  = $test->buildsearchtree($root);
$recordsvalues = array_values($searchtree);
$search['json'] = json_encode($recordsvalues, true);

$data['json'] = $d;
$data['root'] = $root;
echo $OUTPUT->header();
$mform = new local_wb_faq\form\categories($o);
$mform->display();
echo $OUTPUT->render_from_template('local_wb_faq/search', $search);
echo $OUTPUT->render_from_template('local_wb_faq/js', $data);
echo $OUTPUT->footer();
