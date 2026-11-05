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
 * Callbacks of the plugin.
 *
 * @package    local_wb_faq
 * @copyright  2022 Wunderbyte GmbH <info@wunderbyte.at>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

use local_wb_faq\local\access;
use local_wb_faq\local\entry_repository;
use local_wb_faq\wb_faq;

/**
 * Renders the help menu in the navigation bar, if it is activated.
 *
 * @param renderer_base $renderer
 * @return string The HTML
 */
function local_wb_faq_render_navbar_output(renderer_base $renderer): string {
    if (!isloggedin() || isguestuser() || empty(get_config('local_wb_faq', 'usesupport'))) {
        return '';
    }

    return $renderer->render_from_template('local_wb_faq/navbar/support_menu', [
        'links' => local_wb_faq_get_support_links(),
        'minlength' => \local_wb_faq\output\search_view::MIN_QUERY_LENGTH,
        'design' => \local_wb_faq\output\faq_list::get_design(),
    ]);
}

/**
 * Parses the setting with the links of the help menu. It has one "Label|URL" per line.
 *
 * @return array
 */
function local_wb_faq_get_support_links(): array {
    $links = [];
    foreach (preg_split('/\R/', (string)get_config('local_wb_faq', 'supportlinks')) as $line) {
        $parts = array_map('trim', explode('|', $line, 2));
        if (count($parts) !== 2 || $parts[0] === '') {
            continue;
        }
        $url = clean_param($parts[1], PARAM_URL);
        if ($url !== '') {
            $links[] = ['label' => format_string($parts[0]), 'url' => $url];
        }
    }

    return $links;
}

/**
 * Serves the files embedded into questions, to the users who can see the question.
 *
 * @param stdClass $course
 * @param stdClass|null $cm
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options
 * @return bool False if the file does not exist or may not be accessed.
 */
function local_wb_faq_pluginfile(
    $course,
    $cm,
    context $context,
    string $filearea,
    array $args,
    bool $forcedownload,
    array $options = []
): bool {
    if ($context->contextlevel !== CONTEXT_SYSTEM || $filearea !== wb_faq::FILEAREA || count($args) < 2) {
        return false;
    }
    if (!access::can_read()) {
        require_login();
    }

    $itemid = (int)array_shift($args);
    $record = (new entry_repository())->get($itemid);
    if (!$record || !(new access())->can_view_record($record)) {
        return false;
    }

    $filename = array_pop($args);
    $filepath = $args ? '/' . implode('/', $args) . '/' : '/';
    $file = get_file_storage()->get_file($context->id, 'local_wb_faq', $filearea, $itemid, $filepath, $filename);
    if (!$file || $file->is_directory()) {
        return false;
    }

    \core\session\manager::write_close();
    send_stored_file($file, null, 0, $forcedownload, $options);

    return true;
}
