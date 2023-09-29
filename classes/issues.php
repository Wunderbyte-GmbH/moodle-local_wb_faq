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
 * Entities Class to display list of entity records.
 * @package local_wb_faq
 * @author Georg Maißer
 * @copyright 2022 Wunderbyte GmbH
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_wb_faq;

use context_system;
use dml_exception;
use stdClass;

defined('MOODLE_INTERNAL') || die();

global $CFG;

/**
 * Class wb_faq
 *
 * @author Georg Maißer
 * @copyright 2022 Wunderbyte GmbH
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class issues {

    /**
     * Save issues to db. Saves attachments and sends issue via rest.
     * @param stdClass $issue
     * @return integer
     */
    public static function save_issue(stdClass $issue):int {
        global $DB;

        self::transform_data_to_save($issue);

        $issue->id = $DB->insert_record('local_wb_faq_issues', $issue);

        $context = context_system::instance();

        file_save_draft_area_files(
            // The $data->attachments property contains the itemid of the draft file area.
            $issue->attachments,
            // The combination of contextid / component / filearea / itemid
            // form the virtual bucket that file are stored in.
            $context->id,
            'local_wb_faq',
            'supportmessages',
            $issue->id,
            [
                'maxbytes' => 10485760,
                'areamaxbytes' => 10485760,
                'maxfiles' => 2,
            ]
        );

        rest::send_issue($issue);

        return $issue->id ?? 0;
    }

    /**
     * Transform data to save.
     * @param mixed $name
     */
    private static function transform_data_to_save(&$issue) {

        global $USER;

        // Replace 0 with null.
        $issue->priority = empty($issue->priority) ? null : $issue->priority;

        // If there is no userid, we use user.
        $issue->userid = $issue->userid ?? $USER->id;

        // This is always USER.
        $issue->usermodified = $USER->id;

        $now = time();

        $issue->timecreated = $now;
        $issue->timemodified = $now;
    }
}
