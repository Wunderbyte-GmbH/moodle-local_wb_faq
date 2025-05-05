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

use local_wb_faq\output\display_search;
use local_wb_faq\output\faq_list;

require_once('../../config.php');

$delid = optional_param('del', 0, PARAM_INT);
$context = \context_system::instance();
$PAGE->set_context($context);
require_login();
$PAGE->set_url(new moodle_url('/local/wb_faq/index.php', array()));

$title = "FAQ";
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('frontpage');

echo $OUTPUT->header();

$renderer = $PAGE->get_renderer('local_wb_faq');

$data = new display_search($USER->id, 0);
echo $renderer->render_display_search($data);
$data = new faq_list($USER->id, 0, false);
$return = $data->export_for_template($renderer);
echo $renderer->render_list_faq($data);
echo $OUTPUT->footer();
