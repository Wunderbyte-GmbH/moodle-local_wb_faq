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
 * Turns FAQ records into template contexts.
 *
 * @package    local_wb_faq
 * @copyright  2026 Wunderbyte GmbH <info@wunderbyte.at>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace local_wb_faq\output;

use context_system;
use local_wb_faq\local\page_url;
use local_wb_faq\wb_faq;
use stdClass;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/filelib.php');

/**
 * Formats categories and questions for the templates.
 *
 * @package    local_wb_faq
 * @copyright  2026 Wunderbyte GmbH <info@wunderbyte.at>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class record_exporter {
    /** @var int Maximum length of the text shown with a search result. */
    private const EXCERPT_LENGTH = 160;

    /**
     * Returns the template context of a category.
     *
     * @param stdClass $record
     * @param page_url $urls
     * @param bool $canedit
     * @return array
     */
    public static function category(stdClass $record, page_url $urls, bool $canedit): array {
        return [
            'id' => (int)$record->id,
            'title' => format_string($record->title),
            'url' => $urls->category((int)$record->id),
            'sortorder' => (int)$record->sortorder,
            'enabled' => !empty($record->enabled),
            'canedit' => $canedit,
        ];
    }

    /**
     * Returns the template context of a question. The content is only included if the question is open.
     *
     * @param stdClass $record
     * @param page_url $urls
     * @param string $uid
     * @param bool $canedit
     * @param bool $open
     * @return array
     */
    public static function question(stdClass $record, page_url $urls, string $uid, bool $canedit, bool $open): array {
        return [
            'id' => (int)$record->id,
            'uid' => $uid,
            'parentid' => (int)$record->parentid,
            'title' => format_string($record->title),
            'url' => $urls->category((int)$record->parentid, 0, (int)$record->id),
            'sortorder' => (int)$record->sortorder,
            'enabled' => !empty($record->enabled),
            'canedit' => $canedit,
            'open' => $open,
            'content' => $open ? self::format_content($record) : '',
        ];
    }

    /**
     * Returns the formatted content of a question.
     *
     * @param stdClass $record
     * @return string
     */
    public static function format_content(stdClass $record): string {
        $context = context_system::instance();
        $content = file_rewrite_pluginfile_urls(
            (string)$record->content,
            'pluginfile.php',
            $context->id,
            'local_wb_faq',
            wb_faq::FILEAREA,
            (int)$record->id
        );

        return format_text($content, FORMAT_HTML, ['context' => $context]);
    }

    /**
     * Returns a short plain text version of the content.
     *
     * @param stdClass $record
     * @return string
     */
    public static function excerpt(stdClass $record): string {
        return shorten_text(content_to_text((string)$record->content, FORMAT_HTML), self::EXCERPT_LENGTH);
    }

    /**
     * Returns the label of a category in the breadcrumbs. The labels can be replaced by the mapstrings setting.
     *
     * @param string $title
     * @return string
     */
    public static function breadcrumb_label(string $title): string {
        $map = json_decode((string)get_config('local_wb_faq', 'mapstrings'), true);

        return format_string(is_array($map) && isset($map[$title]) ? $map[$title] : $title);
    }
}
