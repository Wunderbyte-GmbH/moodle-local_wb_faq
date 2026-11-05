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
 * A page of search results.
 *
 * @package    local_wb_faq
 * @copyright  2026 Wunderbyte GmbH <info@wunderbyte.at>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace local_wb_faq\output;

use local_wb_faq\local\access;
use local_wb_faq\local\entry_repository;
use local_wb_faq\local\page_url;
use renderable;
use renderer_base;
use templatable;

/**
 * The fragment showing the results of a search.
 *
 * @package    local_wb_faq
 * @copyright  2026 Wunderbyte GmbH <info@wunderbyte.at>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class search_view implements renderable, templatable {
    /** @var int Shorter search terms are not executed. */
    public const MIN_QUERY_LENGTH = 3;

    /** @var access */
    private access $access;

    /** @var page_url */
    private page_url $urls;

    /** @var string */
    private string $uid;

    /** @var int */
    private int $rootid;

    /** @var string */
    private string $query;

    /** @var int */
    private int $page;

    /**
     * Constructor.
     *
     * @param access $access
     * @param page_url $urls
     * @param string $uid Identifies the FAQ instance on the page.
     * @param int $rootid Only this category and the categories below are searched.
     * @param string $query
     * @param int $page
     */
    public function __construct(access $access, page_url $urls, string $uid, int $rootid, string $query, int $page = 0) {
        $this->access = $access;
        $this->urls = $urls;
        $this->uid = $uid;
        $this->rootid = $rootid;
        $this->query = trim($query);
        $this->page = $page;
    }

    /**
     * Returns the data for the template local_wb_faq/search_results.
     *
     * @param renderer_base $output
     * @return array
     */
    public function export_for_template(renderer_base $output): array {
        $perpage = pagination::get_perpage();
        $tooshort = \core_text::strlen($this->query) < self::MIN_QUERY_LENGTH;
        $categoryids = $this->access->get_visible_subtree($this->rootid);

        $total = 0;
        $page = 0;
        $results = [];
        if (!$tooshort && $categoryids) {
            $repository = new entry_repository();
            $filter = $this->access->get_entry_filter();
            $total = $repository->count_search($categoryids, $this->query, $filter);
            $page = pagination::clamp_page($this->page, $total, $perpage);
            foreach ($repository->search($categoryids, $this->query, $filter, $page * $perpage, $perpage) as $record) {
                $results[] = $this->export_result($record);
            }
        }

        return [
            'uid' => $this->uid,
            'root' => $this->rootid,
            'query' => $this->query,
            'page' => $page,
            'total' => $total,
            'tooshort' => $tooshort,
            'minlength' => self::MIN_QUERY_LENGTH,
            'hasresults' => !empty($results),
            'results' => $results,
            'pagination' => pagination::export($total, $page, $perpage, function (int $page): string {
                return $this->urls->search($this->query, $page);
            }),
        ];
    }

    /**
     * Returns the context of one result.
     *
     * @param \stdClass $record
     * @return array
     */
    private function export_result(\stdClass $record): array {
        $iscategory = (string)$record->type === entry_repository::TYPE_CATEGORY;
        $context = $iscategory
            ? record_exporter::category($record, $this->urls, false) + ['uid' => $this->uid, 'content' => '', 'open' => false]
            : record_exporter::question($record, $this->urls, $this->uid, false, false);

        $parentid = (int)$record->parentid;
        $parent = $this->access->get_category($parentid);

        return $context + [
            'iscategory' => $iscategory,
            'parentid' => $parentid,
            'parenttitle' => $parent ? format_string($parent->title) : get_string('faq', 'local_wb_faq'),
            'parenturl' => $this->urls->category($parentid),
            'excerpt' => $iscategory ? '' : record_exporter::excerpt($record),
        ];
    }
}
