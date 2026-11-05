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
 * Changes to the FAQ.
 *
 * @package    local_wb_faq
 * @author     Thomas Winkler
 * @copyright  2022 Wunderbyte GmbH <info@wunderbyte.at>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace local_wb_faq;

use context_system;
use local_wb_faq\event\faq_entry_added;
use local_wb_faq\event\faq_entry_deleted;
use local_wb_faq\event\faq_entry_updated;
use local_wb_faq\local\entry_repository;
use moodle_exception;
use stdClass;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/filelib.php');

/**
 * Creates, updates, deletes and reorders FAQ records, including their files and events.
 *
 * @package    local_wb_faq
 * @author     Thomas Winkler
 * @copyright  2022 Wunderbyte GmbH <info@wunderbyte.at>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class wb_faq {
    /** @var string File area of the question content. */
    public const FILEAREA = 'faq_entry';

    /** @var int Result of toggle_entry_visibility() if the record does not exist. */
    public const TOGGLE_NOTFOUND = 2;

    /**
     * Creates a category or question.
     *
     * @param stdClass $data Form data, optionally with a content_editor property.
     * @return int The new id.
     */
    public static function create_entry(stdClass $data): int {
        $repository = new entry_repository();

        $record = clone $data;
        unset($record->id);
        $record->timecreated = time();
        $record->timemodified = $record->timecreated;
        $record->sortorder = $record->sortorder ?? 0;
        $record->content = $record->content ?? '';

        $record->id = $repository->insert($record);

        if (isset($record->content_editor)) {
            $repository->update(self::save_editor_files($record));
        }

        faq_entry_added::create_for($record->id)->trigger();

        return $record->id;
    }

    /**
     * Updates a category or question.
     *
     * @param stdClass $data Form data, optionally with a content_editor property.
     * @return void
     */
    public static function update_entry(stdClass $data): void {
        $record = clone $data;
        $record->id = (int)$record->id;
        $record->timemodified = time();

        if (isset($record->content_editor)) {
            $record = self::save_editor_files($record);
        }

        (new entry_repository())->update($record);

        faq_entry_updated::create_for($record->id)->trigger();
    }

    /**
     * Deletes a record. A category is deleted together with everything inside of it.
     *
     * @param int $id
     * @return bool False if the record does not exist.
     */
    public static function delete_entry(int $id): bool {
        $repository = new entry_repository();

        $record = $repository->get($id);
        if (!$record) {
            return false;
        }

        $ids = [$id];
        if ((string)$record->type === entry_repository::TYPE_CATEGORY) {
            $categoryids = self::get_category_subtree($id, $repository->get_all_categories());
            $ids = array_merge($categoryids, $repository->get_question_ids_in_categories($categoryids));
        }

        $repository->delete($ids);

        $fs = get_file_storage();
        $contextid = context_system::instance()->id;
        foreach ($ids as $deletedid) {
            $fs->delete_area_files($contextid, 'local_wb_faq', self::FILEAREA, $deletedid);
            faq_entry_deleted::create_for($deletedid)->trigger();
        }

        return true;
    }

    /**
     * Toggles the visibility of a record.
     *
     * @param int $id
     * @return int 0 if it is invisible now, 1 if it is visible now, 2 if it does not exist.
     */
    public static function toggle_entry_visibility(int $id): int {
        $repository = new entry_repository();

        $record = $repository->get($id);
        if (!$record) {
            return self::TOGGLE_NOTFOUND;
        }

        $enabled = empty($record->enabled) ? 1 : 0;
        $repository->update((object)['id' => $id, 'enabled' => $enabled, 'timemodified' => time()]);

        faq_entry_updated::create_for($id)->trigger();

        return $enabled;
    }

    /**
     * Applies a new order to siblings.
     *
     * The given ids can be just one page of a category. The siblings on other pages keep their position, the
     * given ids are rearranged among the positions they occupy.
     *
     * @param int[] $ids
     * @return void
     */
    public static function set_order(array $ids): void {
        if (empty($ids)) {
            return;
        }

        $repository = new entry_repository();
        $first = $repository->get((int)reset($ids));
        if (!$first) {
            throw new moodle_exception('invalidorder', 'local_wb_faq');
        }

        $siblings = $repository->get_sibling_ids((int)$first->parentid, (string)$first->type);
        $ids = array_map('intval', $ids);
        if (array_diff($ids, $siblings) || count(array_unique($ids)) !== count($ids)) {
            throw new moodle_exception('invalidorder', 'local_wb_faq');
        }

        $next = 0;
        foreach ($siblings as $position => $sibling) {
            if (in_array($sibling, $ids, true)) {
                $siblings[$position] = $ids[$next++];
            }
        }

        $total = count($siblings);
        foreach ($siblings as $position => $sibling) {
            $repository->update((object)['id' => $sibling, 'sortorder' => $total - $position - 1]);
        }
    }

    /**
     * Returns the id of a category and of all categories below it.
     *
     * @param int $id
     * @param stdClass[] $categories All categories.
     * @return int[]
     */
    public static function get_category_subtree(int $id, array $categories): array {
        $ids = [$id];
        foreach ($categories as $category) {
            if ((int)$category->parentid === $id) {
                $ids = array_merge($ids, self::get_category_subtree((int)$category->id, $categories));
            }
        }

        return $ids;
    }

    /**
     * Returns the options of the content editor.
     *
     * @return array
     */
    public static function get_textfield_options(): array {
        return [
            'subdirs' => true,
            'context' => context_system::instance(),
            'maxfiles' => EDITOR_UNLIMITED_FILES,
        ];
    }

    /**
     * Moves the files of the editor to their final place and sets the content.
     *
     * @param stdClass $record
     * @return stdClass
     */
    private static function save_editor_files(stdClass $record): stdClass {
        return file_postupdate_standard_editor(
            $record,
            'content',
            self::get_textfield_options(),
            context_system::instance(),
            'local_wb_faq',
            self::FILEAREA,
            $record->id
        );
    }
}
