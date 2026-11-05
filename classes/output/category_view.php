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
 * One category of the FAQ with its subcategories and a page of its questions.
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
 * The fragment which gets replaced when the user moves through the FAQ.
 *
 * @package    local_wb_faq
 * @copyright  2026 Wunderbyte GmbH <info@wunderbyte.at>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class category_view implements renderable, templatable {
    /** @var access */
    private access $access;

    /** @var page_url */
    private page_url $urls;

    /** @var entry_repository */
    private entry_repository $repository;

    /** @var string */
    private string $uid;

    /** @var int The topmost category of this FAQ instance. */
    private int $rootid;

    /** @var int */
    private int $categoryid;

    /** @var int The opened question or 0. */
    private int $entryid;

    /** @var int */
    private int $page;

    /** @var bool */
    private bool $canedit;

    /**
     * Constructor. Throws an exception if the user may not see the requested category or question.
     *
     * @param access $access
     * @param page_url $urls
     * @param string $uid Identifies the FAQ instance on the page.
     * @param int $rootid
     * @param int $categoryid Ignored if a question is given, as its category is used then.
     * @param int $entryid
     * @param int $page Ignored if a question is given, as the page containing it is used then.
     * @param bool $allowedit Whether this view offers editing. Needs the capability too.
     */
    public function __construct(
        access $access,
        page_url $urls,
        string $uid,
        int $rootid,
        int $categoryid,
        int $entryid = 0,
        int $page = 0,
        bool $allowedit = false
    ) {
        $this->access = $access;
        $this->urls = $urls;
        $this->repository = new entry_repository();
        $this->uid = $uid;
        $this->rootid = $rootid;
        $this->entryid = $entryid;
        $this->canedit = $allowedit && $access->can_edit();

        $perpage = pagination::get_perpage();
        $filter = $access->get_entry_filter();

        if ($entryid) {
            $entry = $access->require_question($entryid, $rootid);
            $categoryid = (int)$entry->parentid;
            $page = (int)floor($this->repository->get_question_position($entry, $filter) / $perpage);
        }

        $access->require_category_in_root($categoryid, $rootid);

        $this->categoryid = $categoryid;
        $total = $this->repository->count_questions($categoryid, $filter);
        $this->page = pagination::clamp_page($page, $total, $perpage);
    }

    /**
     * Returns the data for the template local_wb_faq/faq.
     *
     * @param renderer_base $output
     * @return array
     */
    public function export_for_template(renderer_base $output): array {
        $perpage = pagination::get_perpage();
        $filter = $this->access->get_entry_filter();

        $categories = [];
        foreach ($this->access->get_visible_children($this->categoryid) as $category) {
            $categories[] = record_exporter::category($category, $this->urls, $this->canedit);
        }

        $questions = [];
        $records = $this->repository->get_questions($this->categoryid, $filter, $this->page * $perpage, $perpage);
        foreach ($records as $record) {
            $open = (int)$record->id === $this->entryid;
            $questions[] = record_exporter::question($record, $this->urls, $this->uid, $this->canedit, $open);
        }

        $total = $this->repository->count_questions($this->categoryid, $filter);
        $breadcrumbs = $this->get_breadcrumbs();

        return [
            'uid' => $this->uid,
            'root' => $this->rootid,
            'categoryid' => $this->categoryid,
            'entryid' => $this->entryid,
            'page' => $this->page,
            'title' => end($breadcrumbs)['name'],
            'canedit' => $this->canedit,
            'isempty' => empty($categories) && empty($questions),
            'breadcrumbs' => $breadcrumbs,
            'categories' => $categories,
            'entries' => $questions,
            'pagination' => pagination::export($total, $this->page, $perpage, function (int $page): string {
                return $this->urls->category($this->categoryid, $page);
            }),
        ];
    }

    /**
     * Returns the way from the root of this instance to the current category.
     *
     * @return array
     */
    private function get_breadcrumbs(): array {
        $breadcrumbs = [];
        $id = $this->categoryid;
        while ($id !== $this->rootid && ($category = $this->access->get_category($id))) {
            array_unshift($breadcrumbs, [
                'id' => $id,
                'name' => record_exporter::breadcrumb_label($category->title),
                'url' => $this->urls->category($id),
                'active' => $id === $this->categoryid,
            ]);
            $id = (int)$category->parentid;
        }

        $root = $this->access->get_category($this->rootid);
        array_unshift($breadcrumbs, [
            'id' => $this->rootid,
            'name' => $root ? record_exporter::breadcrumb_label($root->title) : get_string('faq', 'local_wb_faq'),
            'url' => $this->urls->category($this->rootid),
            'active' => $this->rootid === $this->categoryid,
        ]);

        return $breadcrumbs;
    }
}
