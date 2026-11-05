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
 * Tests of the permissions.
 *
 * @package    local_wb_faq
 * @copyright  2026 Wunderbyte GmbH <info@wunderbyte.at>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace local_wb_faq;

use advanced_testcase;
use local_wb_faq\local\access;
use moodle_exception;

/**
 * Tests of the permissions.
 *
 * @package    local_wb_faq
 * @copyright  2026 Wunderbyte GmbH <info@wunderbyte.at>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \local_wb_faq\local\access
 */
final class access_test extends advanced_testcase {
    /** @var \local_wb_faq_generator */
    private $faq;

    /**
     * Prepares a test.
     */
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
        $this->faq = $this->getDataGenerator()->get_plugin_generator('local_wb_faq');
    }

    /**
     * Issue #3: Categories without a real course are visible to everybody who can read the FAQ.
     */
    public function test_categories_without_course_are_visible_to_all(): void {
        $none = $this->faq->create_category(['courseid' => 0]);
        $site = $this->faq->create_category(['courseid' => SITEID]);

        foreach ([0, $this->getDataGenerator()->create_user()->id] as $userid) {
            $this->setUser($userid);
            $access = new access();
            $this->assertTrue($access->can_view_category((int)$none->id));
            $this->assertTrue($access->can_view_category((int)$site->id));
        }
    }

    /**
     * Course restrictions depend on the enrolment and on the setting.
     */
    public function test_course_restriction(): void {
        $course = $this->getDataGenerator()->create_course();
        $enrolled = $this->getDataGenerator()->create_and_enrol($course);
        $other = $this->getDataGenerator()->create_user();
        $category = $this->faq->create_category(['courseid' => $course->id]);

        $this->setUser($enrolled);
        $this->assertTrue((new access())->can_view_category((int)$category->id));

        $this->setUser($other);
        $this->assertFalse((new access())->can_view_category((int)$category->id));

        set_config('accesonlyowncourses', 0, 'local_wb_faq');
        $this->assertTrue((new access())->can_view_category((int)$category->id));
    }

    /**
     * The restriction of a category also applies to everything below it.
     */
    public function test_restriction_is_inherited(): void {
        $course = $this->getDataGenerator()->create_course();
        $parent = $this->faq->create_category(['courseid' => $course->id]);
        $child = $this->faq->create_category(['parentid' => $parent->id]);
        $question = $this->faq->create_question(['parentid' => $child->id]);
        $hiddenparent = $this->faq->create_category(['enabled' => 0]);
        $hiddenchild = $this->faq->create_category(['parentid' => $hiddenparent->id]);

        $this->setUser($this->getDataGenerator()->create_user());
        $access = new access();

        $this->assertFalse($access->can_view_category((int)$child->id));
        $this->assertFalse($access->can_view_record($question));
        $this->assertFalse($access->can_view_category((int)$hiddenchild->id));
        $this->assertSame([0], $access->get_visible_subtree(0));
    }

    /**
     * Users who can edit see everything, including hidden records.
     */
    public function test_editors_see_everything(): void {
        $course = $this->getDataGenerator()->create_course();
        $hidden = $this->faq->create_category(['enabled' => 0]);
        $restricted = $this->faq->create_category(['courseid' => $course->id]);
        $question = $this->faq->create_question(['enabled' => 0]);

        $this->setAdminUser();
        $access = new access();

        $this->assertTrue($access->can_edit());
        $this->assertTrue($access->can_view_category((int)$hidden->id));
        $this->assertTrue($access->can_view_category((int)$restricted->id));
        $this->assertTrue($access->can_view_record($question));
        $this->assertSame(['includedisabled' => true, 'courseids' => null], $access->get_entry_filter());
    }

    /**
     * Hidden questions are not visible to normal users.
     */
    public function test_hidden_question(): void {
        $question = $this->faq->create_question(['enabled' => 0]);

        $this->setUser($this->getDataGenerator()->create_user());
        $this->assertFalse((new access())->can_view_record($question));
    }

    /**
     * Whether users who are not logged in can read depends on the setting and on forcelogin.
     */
    public function test_anonymous_access(): void {
        $this->setUser(0);
        set_config('forcelogin', 0);
        $this->assertFalse(access::can_read());

        set_config('allowanonymous', 1, 'local_wb_faq');
        $this->assertTrue(access::can_read());

        set_config('forcelogin', 1);
        $this->assertFalse(access::can_read());
        $this->expectException(\require_login_exception::class);
        access::require_read();
    }

    /**
     * Logged in users can always read.
     */
    public function test_logged_in_users_can_read(): void {
        $this->setUser($this->getDataGenerator()->create_user());
        $this->assertTrue(access::can_read());

        $this->setGuestUser();
        $this->assertTrue(access::can_read());
    }

    /**
     * A category outside of the root of an instance cannot be reached through this instance.
     */
    public function test_root_boundary(): void {
        $root = $this->faq->create_category();
        $inside = $this->faq->create_category(['parentid' => $root->id]);
        $outside = $this->faq->create_category();

        $this->setUser($this->getDataGenerator()->create_user());
        $access = new access();

        $access->require_category_in_root((int)$inside->id, (int)$root->id);
        $this->expectException(moodle_exception::class);
        $access->require_category_in_root((int)$outside->id, (int)$root->id);
    }

    /**
     * Cycles in old data must not cause endless loops.
     */
    public function test_cycle_in_old_data(): void {
        global $DB;

        $first = $this->faq->create_category();
        $second = $this->faq->create_category(['parentid' => $first->id]);
        $DB->set_field('local_wb_faq_entry', 'parentid', $second->id, ['id' => $first->id]);

        $this->setUser($this->getDataGenerator()->create_user());
        $this->assertFalse((new access())->can_view_category((int)$first->id));
    }
}
