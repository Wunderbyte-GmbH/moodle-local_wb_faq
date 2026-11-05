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
 * Pagination of FAQ lists.
 *
 * @package    local_wb_faq
 * @copyright  2026 Wunderbyte GmbH <info@wunderbyte.at>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace local_wb_faq\output;

/**
 * Calculates page numbers and builds the context of the pagination template.
 *
 * @package    local_wb_faq
 * @copyright  2026 Wunderbyte GmbH <info@wunderbyte.at>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class pagination {
    /** @var int Number of items on a page if the setting is invalid. */
    private const DEFAULT_PERPAGE = 10;

    /** @var int Number of page links shown on each side of the current page. */
    private const WINDOW = 2;

    /**
     * Returns the configured number of items on a page.
     *
     * @return int
     */
    public static function get_perpage(): int {
        $perpage = (int)get_config('local_wb_faq', 'perpage');

        return $perpage > 0 ? $perpage : self::DEFAULT_PERPAGE;
    }

    /**
     * Moves a page number into the valid range.
     *
     * @param int $page
     * @param int $total
     * @param int $perpage
     * @return int
     */
    public static function clamp_page(int $page, int $total, int $perpage): int {
        $last = max(0, (int)ceil($total / $perpage) - 1);

        return max(0, min($page, $last));
    }

    /**
     * Builds the template context.
     *
     * @param int $total Number of items.
     * @param int $page Current page, starting at 0.
     * @param int $perpage
     * @param callable $urlbuilder Returns the link for a page number.
     * @return array
     */
    public static function export(int $total, int $page, int $perpage, callable $urlbuilder): array {
        $count = (int)ceil($total / $perpage);

        $pages = [];
        $previous = -1;
        for ($i = 0; $i < $count; $i++) {
            if ($i !== 0 && $i !== $count - 1 && abs($i - $page) > self::WINDOW) {
                continue;
            }
            if ($i - $previous > 1) {
                $pages[] = ['page' => 0, 'label' => 0, 'url' => '', 'active' => false, 'ellipsis' => true];
            }
            $pages[] = ['page' => $i, 'label' => $i + 1, 'url' => $urlbuilder($i), 'active' => $i === $page, 'ellipsis' => false];
            $previous = $i;
        }

        return [
            'haspages' => $count > 1,
            'hasprevious' => $page > 0,
            'previouspage' => max(0, $page - 1),
            'previousurl' => $page > 0 ? $urlbuilder($page - 1) : '',
            'hasnext' => $page < $count - 1,
            'nextpage' => $page + 1,
            'nexturl' => $page < $count - 1 ? $urlbuilder($page + 1) : '',
            'pages' => $pages,
        ];
    }
}
