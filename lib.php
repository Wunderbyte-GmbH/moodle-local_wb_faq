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
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 define('TRANSMISSION_PENDING', 0);
 define('TRANSMISSION_ERROR', 1);
 define('TRANSMISSION_OK', 2);

/**
 * Adds module specific settings to the settings block
 *
 * @param settings_navigation $settings The settings navigation object
 * @param navigation_node $modnode The node to add module settings to
 *
 * $settings is unused, but API requires it. Suppress PHPMD warning.
 *
 */

/**
 * Renders the popup Link.
 *
 * @param renderer_base $renderer
 * @return string The HTML
 */
function local_wb_faq_render_navbar_output(\renderer_base $renderer) {
    global $CFG, $COURSE, $PAGE, $DB;

    // Early bail out conditions.
    if (!isloggedin()) {
        return '';
    }

    $usesupport = get_config('local_wb_faq', 'usesupport');

    if (empty($usesupport)) {
        return '';
    }

    $modal = $renderer->render_from_template('local_wb_faq/navbar/popoverbutton', []);

    // Create the links to the transfer.php.
    $supportvertrieburl = new moodle_url('/local/wb_faq/transfer.php', [
        'type' => 'vertrieb',
    ]);

    // Create the links to the transfer.php.
    $supportausbildungburl = new moodle_url('/local/wb_faq/transfer.php', [
        'type' => 'ausbildung',
    ]);

    // Create the links to the transfer.php.
    $supportstoerungburl = new moodle_url('/local/wb_faq/transfer.php', [
        'type' => 'stoerung',
    ]);

    $supportmyticketsurl = new moodle_url('/local/wb_faq/transfer.php', [
        'type' => 'mymessages',
    ]);

    $teamurl = new moodle_url('/local/wb_faq/transfer.php', [
        'type' => 'team',
    ]);

    $output = '<div class="popover-region nav-link icon-no-margin dropdown">
        <button class="btn btn-secondary dropdown-toggle" type="button"
        id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        '. get_string('support', 'local_wb_faq') .'
        </button>
        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            <a class="dropdown-item" href="#">'
            . $modal . '</a>
            <a class="dropdown-item" href="' . $supportausbildungburl->out() . '" target="_blank">'
                . get_string('supportweiterbildung', 'local_wb_faq') . '</a>
            <a class="dropdown-item" href="' . $supportvertrieburl->out() . '" target="_blank">'
                . get_string('supportvertrieb', 'local_wb_faq') . '</a>
            <a class="dropdown-item" href="' . $supportmyticketsurl->out() . '" target="_blank">'
                . get_string('supportmytickets', 'local_wb_faq') . '</a>
            <a class="dropdown-item" href="' . $supportstoerungburl->out() . '" target="_blank">'
                . get_string('supportstoerung', 'local_wb_faq') . '</a>
            <a class="dropdown-item" href="' . $teamurl->out() . '" target="_blank">'
                . get_string('team', 'local_wb_faq') . '</a>
        </div>
    </div>';

    return $output;
}

/**
 *
 * Get saved files for the page
 *
 * @param mixed $course
 * @param mixed $birecordorcm
 * @param mixed $context
 * @param mixed $filearea
 * @param mixed $args
 * @param bool $forcedownload
 * @param array $options
 */
function local_wb_faq_pluginfile($course, $birecordorcm, $context, $filearea, $args, $forcedownload, array $options = array()) {
    $fs = get_file_storage();

    $filename = array_pop($args);
    $filepath = $args ? '/' . implode('/', $args) . '/' : '/';

    if ($filearea === 'description') {
        if (!$file = $fs->get_file($context->id, 'local_wb_faq', 'entitycontent', 0, $filepath, $filename) or $file->is_directory()) {
            send_file_not_found();
        }
    } else if ($filearea === 'image') {
        $itemid = array_pop($args);
        $file = $fs->get_file($context->id, 'local_wb_faq', $filearea, $itemid, '/', $filename);
        // Todo: Maybe put in fall back image.
    } else {
        $itemid = array_pop($args);
        if (!$file = $fs->get_file($context->id, 'local_wb_faq', $filearea, $itemid, '/', $filename) or $file->is_directory()) {
            send_file_not_found();
        }
    }


    \core\session\manager::write_close();
    send_stored_file($file, null, 0, $forcedownload, $options);
}
