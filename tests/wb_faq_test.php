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
 * Tests of changes to the FAQ.
 *
 * @package    local_wb_faq
 * @copyright  2026 Wunderbyte GmbH <info@wunderbyte.at>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace local_wb_faq;

use advanced_testcase;
use context_system;
use local_wb_faq\event\faq_entry_added;
use local_wb_faq\event\faq_entry_deleted;
use local_wb_faq\event\faq_entry_updated;
use local_wb_faq\local\entry_repository;
use moodle_exception;

/**
 * Tests of changes to the FAQ.
 *
 * @package    local_wb_faq
 * @copyright  2026 Wunderbyte GmbH <info@wunderbyte.at>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \local_wb_faq\wb_faq
 */
final class wb_faq_test extends advanced_testcase {
    /** @var \local_wb_faq_generator */
    private $faq;

    /**
     * Prepares a test.
     */
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
        $this->setAdminUser();
        $this->faq = $this->getDataGenerator()->get_plugin_generator('local_wb_faq');
    }

    /**
     * Creating a question saves the files of the editor and triggers the event.
     */
    public function test_create_question_with_editor(): void {
        global $USER;

        $draftid = file_get_unused_draft_itemid();
        $usercontext = \context_user::instance($USER->id);
        get_file_storage()->create_file_from_string([
            'contextid' => $usercontext->id,
            'component' => 'user',
            'filearea' => 'draft',
            'itemid' => $draftid,
            'filepath' => '/',
            'filename' => 'test.txt',
        ], 'content');

        $sink = $this->redirectEvents();
        $id = wb_faq::create_entry((object)[
            'title' => 'Question',
            'type' => entry_repository::TYPE_QUESTION,
            'parentid' => 0,
            'enabled' => 1,
            'content_editor' => [
                'text' => '<p>Answer</p>',
                'format' => FORMAT_HTML,
                'itemid' => $draftid,
            ],
        ]);
        $events = $sink->get_events();
        $sink->close();

        $record = (new entry_repository())->get($id);
        $this->assertSame('<p>Answer</p>', $record->content);
        $this->assertInstanceOf(faq_entry_added::class, reset($events));
        $this->assertFalse(get_file_storage()->is_area_empty(
            context_system::instance()->id,
            'local_wb_faq',
            wb_faq::FILEAREA,
            $id
        ));
    }

    /**
     * Categories have no editor.
     */
    public function test_create_and_update_category(): void {
        $id = wb_faq::create_entry((object)['title' => 'Category', 'type' => entry_repository::TYPE_CATEGORY, 'parentid' => 0]);

        $sink = $this->redirectEvents();
        wb_faq::update_entry((object)['id' => $id, 'title' => 'Changed']);
        $events = $sink->get_events();
        $sink->close();

        $this->assertSame('Changed', (new entry_repository())->get($id)->title);
        $this->assertInstanceOf(faq_entry_updated::class, reset($events));
    }

    /**
     * Deleting a category removes everything inside of it.
     */
    public function test_delete_category_with_content(): void {
        $parent = $this->faq->create_category();
        $child = $this->faq->create_category(['parentid' => $parent->id]);
        $question = $this->faq->create_question(['parentid' => $child->id]);
        $other = $this->faq->create_question();

        $sink = $this->redirectEvents();
        $this->assertTrue(wb_faq::delete_entry((int)$parent->id));
        $events = $sink->get_events();
        $sink->close();

        $repository = new entry_repository();
        $this->assertNull($repository->get((int)$parent->id));
        $this->assertNull($repository->get((int)$child->id));
        $this->assertNull($repository->get((int)$question->id));
        $this->assertNotNull($repository->get((int)$other->id));
        $this->assertCount(3, $events);
        $this->assertInstanceOf(faq_entry_deleted::class, $events[0]);
    }

    /**
     * Deleting a missing record reports this.
     */
    public function test_delete_missing_entry(): void {
        $this->assertFalse(wb_faq::delete_entry(999999));
    }

    /**
     * Toggling reports the new state.
     */
    public function test_toggle_visibility(): void {
        $question = $this->faq->create_question();

        $this->assertSame(0, wb_faq::toggle_entry_visibility((int)$question->id));
        $this->assertSame(1, wb_faq::toggle_entry_visibility((int)$question->id));
        $this->assertSame(wb_faq::TOGGLE_NOTFOUND, wb_faq::toggle_entry_visibility(999999));
    }

    /**
     * Reordering one page keeps the position of the questions on the other pages.
     */
    public function test_set_order_of_one_page(): void {
        $ids = [];
        foreach ([5, 4, 3, 2, 1] as $sortorder) {
            $ids[] = (int)$this->faq->create_question(['sortorder' => $sortorder])->id;
        }

        // Only the second and third question are on the current page.
        wb_faq::set_order([$ids[2], $ids[1]]);

        $this->assertSame(
            [$ids[0], $ids[2], $ids[1], $ids[3], $ids[4]],
            (new entry_repository())->get_sibling_ids(0, entry_repository::TYPE_QUESTION)
        );
    }

    /**
     * Records of different lists cannot be sorted together.
     */
    public function test_set_order_rejects_foreign_ids(): void {
        $category = $this->faq->create_category();
        $first = $this->faq->create_question();
        $second = $this->faq->create_question(['parentid' => $category->id]);

        $this->expectException(moodle_exception::class);
        wb_faq::set_order([(int)$first->id, (int)$second->id]);
    }

    /**
     * The subtree contains a category and everything below.
     */
    public function test_get_category_subtree(): void {
        $parent = $this->faq->create_category();
        $child = $this->faq->create_category(['parentid' => $parent->id]);
        $grandchild = $this->faq->create_category(['parentid' => $child->id]);
        $this->faq->create_category();

        $subtree = wb_faq::get_category_subtree((int)$parent->id, (new entry_repository())->get_all_categories());

        $this->assertEqualsCanonicalizing([(int)$parent->id, (int)$child->id, (int)$grandchild->id], $subtree);
    }
}
