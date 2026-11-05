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
 * Tests of the data access.
 *
 * @package    local_wb_faq
 * @copyright  2026 Wunderbyte GmbH <info@wunderbyte.at>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace local_wb_faq;

use advanced_testcase;
use local_wb_faq\local\entry_repository;

/**
 * Tests of the data access.
 *
 * @package    local_wb_faq
 * @copyright  2026 Wunderbyte GmbH <info@wunderbyte.at>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \local_wb_faq\local\entry_repository
 */
final class entry_repository_test extends advanced_testcase {
    /** @var array A filter which shows everything. */
    private const ALL = ['includedisabled' => true, 'courseids' => null];

    /** @var array A filter which hides disabled records. */
    private const ENABLED = ['includedisabled' => false, 'courseids' => null];

    /** @var \local_wb_faq_generator */
    private $faq;

    /** @var entry_repository */
    private $repository;

    /**
     * Prepares a test.
     */
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
        $this->faq = $this->getDataGenerator()->get_plugin_generator('local_wb_faq');
        $this->repository = new entry_repository();
    }

    /**
     * A missing record returns null.
     */
    public function test_get(): void {
        $question = $this->faq->create_question(['title' => 'Q']);

        $this->assertSame('Q', $this->repository->get((int)$question->id)->title);
        $this->assertNull($this->repository->get(999999));
    }

    /**
     * Questions come in display order and can be paged and filtered.
     */
    public function test_questions_paging_and_order(): void {
        $category = $this->faq->create_category();
        $parentid = (int)$category->id;
        $a = $this->faq->create_question(['parentid' => $parentid, 'sortorder' => 3, 'title' => 'A']);
        $b = $this->faq->create_question(['parentid' => $parentid, 'sortorder' => 2, 'title' => 'B']);
        $c = $this->faq->create_question(['parentid' => $parentid, 'sortorder' => 1, 'title' => 'C', 'enabled' => 0]);
        $this->faq->create_question(['title' => 'Elsewhere']);

        $this->assertSame(3, $this->repository->count_questions($parentid, self::ALL));
        $this->assertSame(2, $this->repository->count_questions($parentid, self::ENABLED));

        $page = $this->repository->get_questions($parentid, self::ALL, 1, 2);
        $this->assertSame(['B', 'C'], array_column($page, 'title'));

        $this->assertSame(0, $this->repository->get_question_position($a, self::ALL));
        $this->assertSame(1, $this->repository->get_question_position($b, self::ALL));
        $this->assertSame(2, $this->repository->get_question_position($c, self::ALL));
    }

    /**
     * Course filters keep records without a course and those of the given courses.
     */
    public function test_course_filter(): void {
        $this->faq->create_question(['title' => 'None']);
        $this->faq->create_question(['title' => 'Site', 'courseid' => SITEID]);
        $this->faq->create_question(['title' => 'Mine', 'courseid' => 50]);
        $this->faq->create_question(['title' => 'Other', 'courseid' => 51]);

        $filter = ['includedisabled' => false, 'courseids' => [50]];
        $titles = array_column($this->repository->get_questions(0, $filter, 0, 10), 'title');
        sort($titles);

        $this->assertSame(['Mine', 'None', 'Site'], $titles);
    }

    /**
     * The search looks into titles and contents, inside of the given categories.
     */
    public function test_search(): void {
        $inside = $this->faq->create_category(['title' => 'Router category']);
        $outside = $this->faq->create_category();
        $this->faq->create_question(['parentid' => $inside->id, 'title' => 'Router title']);
        $this->faq->create_question(['parentid' => $inside->id, 'title' => 'Other', 'content' => 'About the ROUTER']);
        $this->faq->create_question(['parentid' => $inside->id, 'title' => 'Hidden router', 'enabled' => 0]);
        $this->faq->create_question(['parentid' => $outside->id, 'title' => 'Router outside']);

        $categoryids = [0, (int)$inside->id];
        $this->assertSame(3, $this->repository->count_search($categoryids, 'router', self::ENABLED));

        $results = $this->repository->search($categoryids, 'router', self::ENABLED, 0, 10);
        $this->assertSame('Router category', $results[0]->title);
        $this->assertCount(1, $this->repository->search($categoryids, 'router', self::ENABLED, 2, 10));
    }

    /**
     * Umlauts can be written in both ways.
     */
    public function test_search_umlauts(): void {
        $this->faq->create_question(['title' => 'Überprüfung', 'content' => '']);
        $this->faq->create_question(['title' => 'Mueller', 'content' => '']);

        $this->assertSame(1, $this->repository->count_search([0], 'Ueberpruefung', self::ENABLED));
        $this->assertSame(1, $this->repository->count_search([0], 'Überprüfung', self::ENABLED));
        $this->assertSame(1, $this->repository->count_search([0], 'Müller', self::ENABLED));
    }

    /**
     * Wildcards in the search term have no special meaning.
     */
    public function test_search_escapes_wildcards(): void {
        $this->faq->create_question(['title' => 'Plain', 'content' => '']);

        $this->assertSame(0, $this->repository->count_search([0], 'P%n', self::ENABLED));
    }

    /**
     * Categories are found by their title, if this is unique.
     */
    public function test_find_category_id_by_title(): void {
        $category = $this->faq->create_category(['title' => 'Unique']);
        $this->faq->create_category(['title' => 'Twice']);
        $this->faq->create_category(['title' => 'Twice']);

        $this->assertSame((int)$category->id, $this->repository->find_category_id_by_title('Unique'));
        $this->assertNull($this->repository->find_category_id_by_title('Twice'));
        $this->assertNull($this->repository->find_category_id_by_title('Missing'));
    }
}
