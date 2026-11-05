<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * The FAQ with its edit controls, for users who can edit it.
 *
 * @package    local_wb_faq
 * @copyright  2022 Wunderbyte GmbH <info@wunderbyte.at>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

// phpcs:ignore moodle.Files.RequireLogin.Missing -- The login check below depends on a setting.
require_once(__DIR__ . '/../../config.php');

use local_wb_faq\local\access;
use local_wb_faq\output\faq_list;

$url = new moodle_url('/local/wb_faq/wb_faq.php');
$PAGE->set_context(context_system::instance());
$PAGE->set_url($url);

// Whether users who are not logged in can read the FAQ depends on the settings.
if (!access::can_read()) {
    require_login();
}

$title = get_string('faq', 'local_wb_faq');
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('standard');

$faq = faq_list::from_request($url, true, null);

echo $OUTPUT->header();
echo $PAGE->get_renderer('local_wb_faq')->render($faq);
echo $OUTPUT->footer();
