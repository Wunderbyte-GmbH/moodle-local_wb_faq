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
    if (!isloggedin() || isguestuser()) {
        return '';
    }

    $usesupport = get_config('local_wb_faq', 'usesupport');

    if (empty($usesupport)) {
        return '';
    }

    $modal = $renderer->render_from_template('local_wb_faq/navbar/popoverbutton', []);

    // Determine if we have a group to submit.
    $context = $PAGE->context;

    switch ($context->contextlevel) {
        case CONTEXT_COURSE:

            $section = optional_param('section', null, PARAM_INT);

            // If there is no section, we join on the coursename.
            if ($section === null) {
                $where = "JOIN {course} c ON c.id=cs.course
                          JOIN {local_wb_faq_entry} wfe ON wfe.title=c.fullname
                          WHERE cs.course=:course
                          LIMIT 1";
            } else {
                // If there is one, we can join on the section name.
                $where = "LEFT JOIN {local_wb_faq_entry} wfe ON wfe.title=cs.name
                          WHERE cs.section = :section AND cs.course=:course
                          LIMIT 1";
            }
            $sql = "SELECT DISTINCT cs.id, wfe.module, wfe.supplement as supplement
                    FROM {course_sections} cs
                    $where";
            $params = [
                'section' => $section,
                'course' => $COURSE->id,

            ];
            $record = $DB->get_record_sql($sql, $params);
            break;
        case CONTEXT_MODULE:
            list($course, $cm) = get_course_and_cm_from_cmid($context->instanceid);
            $sql = "SELECT cs.id, wfe.module, wfe.supplement as supplement
                    FROM {course_sections} cs
                    LEFT JOIN {local_wb_faq_entry} wfe on wfe.title=cs.name
                    WHERE cs.id = :sectionid";
            $params = [
                'sectionid' => $cm->section,
            ];
            $record = $DB->get_record_sql($sql, $params);
            break;
        case CONTEXT_COURSECAT:

            $sql = "SELECT cc.id, wfe.module, wfe.supplement as supplement
                    FROM {course_categories} cc
                    LEFT JOIN {local_wb_faq_entry} wfe on wfe.title=cc.name
                    WHERE cc.id=:categoryid";
            $params = [
                'categoryid' => $context->instanceid,
            ];
            $record = $DB->get_record_sql($sql, $params);

            break;
        case CONTEXT_SYSTEM:
            $record = (object)[
                'group' => 'SONST',
                'module' => 'Sons',
            ];
            break;
    }

    if (!is_object($record)) {
        $record = new stdClass();
    } else {
        // In mysql family, we can't use group as column name.
        $record->group = $record->supplement ?? null;
        unset($record->supplement);
    }

    $record->group = $record->group ?? 'SONST';
    $record->module = $record->module ?? 'Sons';

    // Create the links to the transfer.php.
    $supportvertrieburl = new moodle_url('/local/wb_faq/transfer.php', [
        'type' => 'vertrieb',
        'group' => $record->group,
        'module' => $record->module,
    ]);

    $supportmyticketsurl = new moodle_url('/local/wb_faq/transfer.php', [
        'type' => 'mymessages',
        'group' => $record->group,
        'module' => $record->module,
    ]);

    $output = '<div class="popover-region nav-link icon-no-margin dropdown">
        <button class="btn btn-secondary dropdown-toggle" type="button"
        id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        '. get_string('support', 'local_wb_faq') .'
        </button>
        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            <a class="dropdown-item" href="#">'
            . $modal . '</a>
            <a class="dropdown-item" href="' . $supportvertrieburl->out() . '" target="_blank">'
                . get_string('supportweiterbildung', 'local_wb_faq') . '</a>
            <a class="dropdown-item" href="' . $supportvertrieburl->out() . '" target="_blank">'
                . get_string('supportvertrieb', 'local_wb_faq') . '</a>
            <a class="dropdown-item" href="' . $supportmyticketsurl->out() . '" target="_blank">'
                . get_string('supportmytickets', 'local_wb_faq') . '</a>
            <a class="dropdown-item" href="' . $supportmyticketsurl->out() . '" target="_blank">'
                . get_string('supportstoerung', 'local_wb_faq') . '</a>
            <a class="dropdown-item" href="https://services.comm-unity.at/extranet/#!/mitarbeiter">'
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
    }

    \core\session\manager::write_close();
    send_stored_file($file, null, 0, $forcedownload, $options);
}
