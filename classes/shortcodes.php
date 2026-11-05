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
 * Shortcodes of the plugin.
 *
 * @package    local_wb_faq
 * @copyright  2022 Wunderbyte GmbH <info@wunderbyte.at>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace local_wb_faq;

use local_wb_faq\local\access;
use local_wb_faq\local\entry_repository;
use local_wb_faq\output\faq_list;
use moodle_url;

/**
 * Embeds the FAQ into other pages.
 *
 * @package    local_wb_faq
 * @copyright  2022 Wunderbyte GmbH <info@wunderbyte.at>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class shortcodes {
    /**
     * Renders the shortcode [faq]. Optional attributes are cat, nosearch, nolist and allowedit.
     *
     * @param string $shortcode
     * @param array $args
     * @param string|null $content
     * @param object $env
     * @param callable $next
     * @return string
     */
    public static function show_faq($shortcode, $args, $content, $env, $next): string {
        global $PAGE;

        if (!access::can_read()) {
            return get_string('loginrequired', 'local_wb_faq');
        }

        $rootid = 0;
        $category = (string)($args['cat'] ?? '');
        if ($category !== '') {
            $rootid = (new entry_repository())->find_category_id_by_title($category);
            if ($rootid === null) {
                return get_string('categorynotfound', 'local_wb_faq', s($category));
            }
            if (!(new access())->can_view_category($rootid)) {
                return '';
            }
        }

        $faq = new faq_list(new moodle_url($PAGE->url), uniqid(), $rootid, [
            'showsearch' => empty($args['nosearch']),
            'showlist' => empty($args['nolist']),
            'allowedit' => !empty($args['allowedit']),
        ]);

        return $PAGE->get_renderer('local_wb_faq')->render($faq);
    }
}
