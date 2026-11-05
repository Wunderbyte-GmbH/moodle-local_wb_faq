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
 * Decides what the current user may see.
 *
 * @package    local_wb_faq
 * @copyright  2026 Wunderbyte GmbH <info@wunderbyte.at>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace local_wb_faq\local;

use context_system;
use moodle_exception;
use require_login_exception;
use stdClass;

/**
 * Visibility rules of the FAQ, evaluated for the current user.
 *
 * A category is visible if it and all of its parents are visible. A record is visible if it is enabled and
 * if its course does not restrict it. Records without a course, or linked to the site course, are not
 * restricted. Users who can edit the FAQ see everything.
 *
 * @package    local_wb_faq
 * @copyright  2026 Wunderbyte GmbH <info@wunderbyte.at>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class access {
    /** @var entry_repository */
    private entry_repository $repository;

    /** @var bool */
    private bool $canedit;

    /** @var stdClass[] All categories in display order, indexed by id. */
    private array $categories;

    /** @var int[]|null Ids of the courses of the user, loaded on demand. */
    private ?array $courseids = null;

    /** @var bool[] Already evaluated categories. */
    private array $visible = [0 => true];

    /**
     * Constructor.
     *
     * @param entry_repository|null $repository
     */
    public function __construct(?entry_repository $repository = null) {
        $this->repository = $repository ?? new entry_repository();
        $this->canedit = has_capability('local/wb_faq:canedit', context_system::instance());
        $this->categories = $this->repository->get_all_categories();
    }

    /**
     * Whether users who are not logged in may read the FAQ.
     *
     * @return bool
     */
    public static function allows_anonymous(): bool {
        global $CFG;

        return !empty(get_config('local_wb_faq', 'allowanonymous')) && empty($CFG->forcelogin);
    }

    /**
     * Whether the current user may read the FAQ at all.
     *
     * @return bool
     */
    public static function can_read(): bool {
        return isloggedin() || self::allows_anonymous();
    }

    /**
     * Throws an exception if the current user may not read the FAQ. Meant for webservices.
     *
     * @return void
     */
    public static function require_read(): void {
        if (!self::can_read()) {
            throw new require_login_exception(get_string('loginrequired', 'local_wb_faq'));
        }
    }

    /**
     * Whether the current user may edit the FAQ.
     *
     * @return bool
     */
    public function can_edit(): bool {
        return $this->canedit;
    }

    /**
     * Whether the current user may see this category. 0 stands for the top level.
     *
     * @param int $categoryid
     * @return bool
     */
    public function can_view_category(int $categoryid): bool {
        if (isset($this->visible[$categoryid])) {
            return $this->visible[$categoryid];
        }

        $category = $this->categories[$categoryid] ?? null;
        // A cycle cannot be saved through the forms. If there is one in old data, it must not loop forever.
        $this->visible[$categoryid] = false;
        if ($category && $this->is_record_visible($category)) {
            $this->visible[$categoryid] = $this->can_view_category((int)$category->parentid);
        }

        return $this->visible[$categoryid];
    }

    /**
     * Whether the current user may see this question or category.
     *
     * @param stdClass $record
     * @return bool
     */
    public function can_view_record(stdClass $record): bool {
        if ((string)$record->type === entry_repository::TYPE_CATEGORY) {
            return $this->can_view_category((int)$record->id);
        }

        return $this->is_record_visible($record) && $this->can_view_category((int)$record->parentid);
    }

    /**
     * Returns the category with this id.
     *
     * @param int $categoryid
     * @return stdClass|null
     */
    public function get_category(int $categoryid): ?stdClass {
        return $this->categories[$categoryid] ?? null;
    }

    /**
     * Returns the visible direct children of a category.
     *
     * @param int $parentid
     * @return stdClass[]
     */
    public function get_visible_children(int $parentid): array {
        $children = [];
        foreach ($this->categories as $category) {
            if ((int)$category->parentid === $parentid && $this->can_view_category((int)$category->id)) {
                $children[] = $category;
            }
        }

        return $children;
    }

    /**
     * Returns the ids of the visible categories of a subtree, including its root.
     *
     * @param int $rootid
     * @return int[]
     */
    public function get_visible_subtree(int $rootid): array {
        if (!$this->can_view_category($rootid)) {
            return [];
        }

        $ids = [$rootid];
        foreach ($this->get_visible_children($rootid) as $child) {
            $ids = array_merge($ids, $this->get_visible_subtree((int)$child->id));
        }

        return $ids;
    }

    /**
     * Returns the filter for the repository which matches the rules of this class.
     *
     * @return array
     */
    public function get_entry_filter(): array {
        $restricted = !$this->canedit && !empty(get_config('local_wb_faq', 'accesonlyowncourses'));

        return [
            'includedisabled' => $this->canedit,
            'courseids' => $restricted ? $this->get_course_ids() : null,
        ];
    }

    /**
     * Throws an exception unless the category is visible and inside the subtree of the root.
     * Missing and forbidden categories are not told apart, so that the existence of a category does not leak.
     *
     * @param int $categoryid
     * @param int $rootid
     * @return void
     */
    public function require_category_in_root(int $categoryid, int $rootid): void {
        if (!in_array($categoryid, $this->get_visible_subtree($rootid), true)) {
            throw new moodle_exception('entrynotfound', 'local_wb_faq');
        }
    }

    /**
     * Returns the record of a question. Throws an exception if it does not exist or is not visible.
     *
     * @param int $entryid
     * @param int $rootid
     * @return stdClass
     */
    public function require_question(int $entryid, int $rootid = 0): stdClass {
        $entry = $this->repository->get($entryid);
        if (!$entry || (string)$entry->type !== entry_repository::TYPE_QUESTION || !$this->can_view_record($entry)) {
            throw new moodle_exception('entrynotfound', 'local_wb_faq');
        }
        $this->require_category_in_root((int)$entry->parentid, $rootid);

        return $entry;
    }

    /**
     * Checks enabled state and course restriction of a single record, ignoring its parents.
     *
     * @param stdClass $record
     * @return bool
     */
    private function is_record_visible(stdClass $record): bool {
        if ($this->canedit) {
            return true;
        }
        if (empty($record->enabled)) {
            return false;
        }

        $courseid = (int)$record->courseid;
        if ($courseid <= 0 || $courseid === (int)SITEID || empty(get_config('local_wb_faq', 'accesonlyowncourses'))) {
            return true;
        }

        return in_array($courseid, $this->get_course_ids(), true);
    }

    /**
     * Returns the ids of the courses the user can access.
     *
     * @return int[]
     */
    private function get_course_ids(): array {
        if ($this->courseids === null) {
            $courses = isloggedin() ? enrol_get_my_courses(['id'], null, 0, [], true) : [];
            $this->courseids = array_map('intval', array_keys($courses));
        }

        return $this->courseids;
    }
}
