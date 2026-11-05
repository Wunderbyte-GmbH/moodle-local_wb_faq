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
 * Return structures shared by the webservices.
 *
 * @package    local_wb_faq
 * @copyright  2026 Wunderbyte GmbH <info@wunderbyte.at>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace local_wb_faq\external;

use context_system;
use core_external\external_api;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use local_wb_faq\local\access;
use local_wb_faq\local\page_url;
use moodle_url;

/**
 * Describes the template contexts returned by the webservices.
 *
 * @package    local_wb_faq
 * @copyright  2026 Wunderbyte GmbH <info@wunderbyte.at>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class structures {
    /**
     * Checks the access of a webservice which only reads, and sets up the page for rendering.
     *
     * The validation of the context needs a login, so it cannot be used if the FAQ is readable without one.
     *
     * @return void
     */
    public static function validate_read_access(): void {
        global $PAGE;

        access::require_read();

        if (isloggedin()) {
            external_api::validate_context(context_system::instance());
        } else {
            $PAGE->set_context(context_system::instance());
        }
    }

    /**
     * Returns the urls helper for the page url sent by the browser.
     *
     * @param string $pageurl
     * @return page_url
     */
    public static function page_urls(string $pageurl): page_url {
        return new page_url(new moodle_url($pageurl !== '' ? $pageurl : '/local/wb_faq/index.php'));
    }

    /**
     * Describes the pagination.
     *
     * @return external_single_structure
     */
    public static function pagination(): external_single_structure {
        return new external_single_structure([
            'haspages' => new external_value(PARAM_BOOL, 'Whether there is more than one page'),
            'hasprevious' => new external_value(PARAM_BOOL, 'Whether there is a previous page'),
            'previouspage' => new external_value(PARAM_INT, 'Number of the previous page'),
            'previousurl' => new external_value(PARAM_URL, 'Link to the previous page'),
            'hasnext' => new external_value(PARAM_BOOL, 'Whether there is a next page'),
            'nextpage' => new external_value(PARAM_INT, 'Number of the next page'),
            'nexturl' => new external_value(PARAM_URL, 'Link to the next page'),
            'pages' => new external_multiple_structure(new external_single_structure([
                'page' => new external_value(PARAM_INT, 'Page number, starting at 0'),
                'label' => new external_value(PARAM_INT, 'Displayed page number'),
                'url' => new external_value(PARAM_URL, 'Link to the page'),
                'active' => new external_value(PARAM_BOOL, 'Whether this is the current page'),
                'ellipsis' => new external_value(PARAM_BOOL, 'Whether this only stands for skipped pages'),
            ])),
        ]);
    }

    /**
     * Returns the fields shared by categories and questions.
     *
     * @return array
     */
    public static function record_fields(): array {
        return [
            'id' => new external_value(PARAM_INT, 'Id'),
            'title' => new external_value(PARAM_RAW, 'Formatted title'),
            'url' => new external_value(PARAM_URL, 'Deep link'),
            'sortorder' => new external_value(PARAM_INT, 'Sort order'),
            'enabled' => new external_value(PARAM_BOOL, 'Whether it is visible to all users'),
            'canedit' => new external_value(PARAM_BOOL, 'Whether edit controls are shown'),
        ];
    }

    /**
     * Returns the fields of a question.
     *
     * @return array
     */
    public static function question_fields(): array {
        return self::record_fields() + [
            'uid' => new external_value(PARAM_ALPHANUMEXT, 'Identifier of the FAQ instance'),
            'parentid' => new external_value(PARAM_INT, 'Id of the category'),
            'open' => new external_value(PARAM_BOOL, 'Whether the question is opened'),
            'content' => new external_value(PARAM_RAW, 'Formatted content, only set for an opened question'),
        ];
    }
}
