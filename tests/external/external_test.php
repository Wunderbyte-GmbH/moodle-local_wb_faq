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
 * Tests of the webservices.
 *
 * @package    local_wb_faq
 * @copyright  2026 Wunderbyte GmbH <info@wunderbyte.at>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace local_wb_faq\external;

use advanced_testcase;
use core_external\external_api;
use local_wb_faq\local\entry_repository;
use moodle_exception;
use required_capability_exception;
use require_login_exception;

/**
 * Tests of the webservices. All results are checked against their return description.
 *
 * @package    local_wb_faq
 * @copyright  2026 Wunderbyte GmbH <info@wunderbyte.at>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \local_wb_faq\external\get_category
 * @covers     \local_wb_faq\external\get_entry
 * @covers     \local_wb_faq\external\search
 * @covers     \local_wb_faq\external\delete_entry
 * @covers     \local_wb_faq\external\toggle_entry_visibility
 * @covers     \local_wb_faq\external\set_wbfaq_order
 */
final class external_test extends advanced_testcase {
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
     * Calls get_category like the browser does.
     *
     * @param array $args
     * @return array
     */
    private function get_category(array $args = []): array {
        $args += ['uid' => 'test', 'pageurl' => '/local/wb_faq/index.php'];
        $result = get_category::execute(
            $args['categoryid'] ?? 0,
            $args['rootid'] ?? 0,
            $args['entryid'] ?? 0,
            $args['page'] ?? 0,
            $args['allowedit'] ?? false,
            $args['uid'],
            $args['pageurl']
        );

        return external_api::clean_returnvalue(get_category::execute_returns(), $result);
    }

    /**
     * Calls search like the browser does.
     *
     * @param string $query
     * @param int $rootid
     * @param int $page
     * @return array
     */
    private function search(string $query, int $rootid = 0, int $page = 0): array {
        $result = search::execute($query, $rootid, $page, 'test', '/local/wb_faq/index.php');

        return external_api::clean_returnvalue(search::execute_returns(), $result);
    }

    /**
     * The fragment of a category contains its children, but no answers of closed questions.
     */
    public function test_get_category(): void {
        $parent = $this->faq->create_category(['title' => 'Parent']);
        $child = $this->faq->create_category(['title' => 'Child', 'parentid' => $parent->id]);
        $question = $this->faq->create_question(['title' => 'Question', 'parentid' => $parent->id]);

        $this->setUser($this->getDataGenerator()->create_user());
        $result = $this->get_category(['categoryid' => (int)$parent->id]);

        $this->assertSame('Parent', $result['title']);
        $this->assertSame((int)$child->id, $result['categories'][0]['id']);
        $this->assertSame((int)$question->id, $result['entries'][0]['id']);
        $this->assertSame('', $result['entries'][0]['content']);
        $this->assertSame(['FAQ', 'Parent'], array_column($result['breadcrumbs'], 'name'));
        $this->assertFalse($result['canedit']);
        $this->assertStringContainsString('faqcat=' . $child->id, $result['categories'][0]['url']);
    }

    /**
     * Issue #2: An empty FAQ is a valid state, and offers editing to editors.
     */
    public function test_get_category_of_empty_faq(): void {
        $this->setAdminUser();
        $result = $this->get_category(['allowedit' => true]);

        $this->assertTrue($result['isempty']);
        $this->assertTrue($result['canedit']);
        $this->assertSame(0, $result['categoryid']);
    }

    /**
     * Users without the capability cannot get edit controls.
     */
    public function test_allowedit_needs_capability(): void {
        $this->setUser($this->getDataGenerator()->create_user());

        $this->assertFalse($this->get_category(['allowedit' => true])['canedit']);
    }

    /**
     * A deep link to a question opens it on the correct page.
     */
    public function test_get_category_with_entry_finds_page(): void {
        set_config('perpage', 2, 'local_wb_faq');
        $category = $this->faq->create_category();
        $last = null;
        foreach ([5, 4, 3, 2, 1] as $sortorder) {
            $last = $this->faq->create_question([
                'parentid' => $category->id,
                'sortorder' => $sortorder,
                'content' => '<p>Text</p>',
            ]);
        }

        $this->setUser($this->getDataGenerator()->create_user());
        $result = $this->get_category(['entryid' => (int)$last->id]);

        $this->assertSame((int)$category->id, $result['categoryid']);
        $this->assertSame(2, $result['page']);
        $this->assertTrue($result['entries'][0]['open']);
        $this->assertStringContainsString('Text', $result['entries'][0]['content']);
        $this->assertTrue($result['pagination']['haspages']);
        $this->assertFalse($result['pagination']['hasnext']);
    }

    /**
     * Pages out of range are moved into the valid range.
     */
    public function test_invalid_page_is_clamped(): void {
        set_config('perpage', 2, 'local_wb_faq');
        foreach ([1, 2, 3] as $number) {
            $this->faq->create_question(['title' => "Q$number"]);
        }

        $this->setUser($this->getDataGenerator()->create_user());

        $this->assertSame(1, $this->get_category(['page' => 99])['page']);
        $this->assertSame(0, $this->get_category(['page' => -5])['page']);
    }

    /**
     * Forbidden categories look like missing ones.
     */
    public function test_get_category_denied(): void {
        $course = $this->getDataGenerator()->create_course();
        $category = $this->faq->create_category(['courseid' => $course->id]);

        $this->setUser($this->getDataGenerator()->create_user());

        $this->expectException(moodle_exception::class);
        $this->expectExceptionMessage(get_string('entrynotfound', 'local_wb_faq'));
        $this->get_category(['categoryid' => (int)$category->id]);
    }

    /**
     * An instance cannot be used to leave its root.
     */
    public function test_get_category_outside_of_root(): void {
        $root = $this->faq->create_category();
        $outside = $this->faq->create_category();

        $this->setUser($this->getDataGenerator()->create_user());

        $this->expectException(moodle_exception::class);
        $this->get_category(['categoryid' => (int)$outside->id, 'rootid' => (int)$root->id]);
    }

    /**
     * Without a login and without the setting, nothing can be read.
     *
     * @dataProvider read_functions_provider
     * @param string $function
     */
    public function test_read_functions_need_login(string $function): void {
        $question = $this->faq->create_question();
        $this->setUser(0);

        $this->expectException(require_login_exception::class);
        $this->call_read_function($function, (int)$question->id);
    }

    /**
     * With the setting, the same calls work without a login.
     *
     * @dataProvider read_functions_provider
     * @param string $function
     */
    public function test_read_functions_with_anonymous_access(string $function): void {
        $question = $this->faq->create_question(['title' => 'Public question']);
        set_config('forcelogin', 0);
        set_config('allowanonymous', 1, 'local_wb_faq');
        $this->setUser(0);

        $this->assertNotEmpty($this->call_read_function($function, (int)$question->id));
    }

    /**
     * Returns the webservices for reading.
     *
     * @return array
     */
    public static function read_functions_provider(): array {
        return [['get_category'], ['get_entry'], ['search']];
    }

    /**
     * Calls a webservice for reading.
     *
     * @param string $function
     * @param int $questionid
     * @return array
     */
    private function call_read_function(string $function, int $questionid): array {
        switch ($function) {
            case 'get_category':
                return get_category::execute();
            case 'get_entry':
                return get_entry::execute($questionid);
            default:
                return search::execute('question');
        }
    }

    /**
     * Anonymous users do not get restricted content.
     */
    public function test_anonymous_users_do_not_see_restricted_content(): void {
        $course = $this->getDataGenerator()->create_course();
        $this->faq->create_category(['title' => 'Public category']);
        $restricted = $this->faq->create_category(['title' => 'Restricted category', 'courseid' => $course->id]);
        $question = $this->faq->create_question(['title' => 'Restricted question', 'parentid' => $restricted->id]);

        set_config('forcelogin', 0);
        set_config('allowanonymous', 1, 'local_wb_faq');
        $this->setUser(0);

        $this->assertSame(['Public category'], array_column($this->get_category()['categories'], 'title'));
        $this->assertSame(0, $this->search('Restricted')['total']);

        $this->expectException(moodle_exception::class);
        get_entry::execute((int)$question->id);
    }

    /**
     * The answer of a question gets loaded and formatted.
     */
    public function test_get_entry(): void {
        $question = $this->faq->create_question(['content' => '<p>Answer<script>alert(1)</script></p>']);

        $this->setUser($this->getDataGenerator()->create_user());
        $result = external_api::clean_returnvalue(get_entry::execute_returns(), get_entry::execute((int)$question->id));

        $this->assertStringContainsString('Answer', $result['content']);
        $this->assertStringNotContainsString('<script', $result['content']);
    }

    /**
     * Hidden questions and categories cannot be loaded directly.
     */
    public function test_get_entry_hidden(): void {
        $question = $this->faq->create_question(['enabled' => 0]);

        $this->setUser($this->getDataGenerator()->create_user());

        $this->expectException(moodle_exception::class);
        get_entry::execute((int)$question->id);
    }

    /**
     * The search only returns visible results below the root, one page at a time.
     */
    public function test_search_results(): void {
        set_config('perpage', 2, 'local_wb_faq');
        $root = $this->faq->create_category(['title' => 'Root']);
        $child = $this->faq->create_category(['title' => 'Router child', 'parentid' => $root->id]);
        $this->faq->create_question(['title' => 'Router one', 'parentid' => $child->id]);
        $this->faq->create_question(['title' => 'Router two', 'parentid' => $child->id]);
        $this->faq->create_question(['title' => 'Router hidden', 'parentid' => $child->id, 'enabled' => 0]);
        $this->faq->create_question(['title' => 'Router outside']);

        $this->setUser($this->getDataGenerator()->create_user());
        $result = $this->search('router', (int)$root->id);

        $this->assertSame(3, $result['total']);
        $this->assertSame(['Router child', 'Router one'], array_column($result['results'], 'title'));
        $this->assertTrue($result['results'][0]['iscategory']);
        $this->assertSame('Router child', $result['results'][1]['parenttitle']);
        $this->assertTrue($result['pagination']['hasnext']);

        $this->assertSame(['Router two'], array_column($this->search('router', (int)$root->id, 1)['results'], 'title'));
    }

    /**
     * Too short search terms are not executed.
     */
    public function test_search_too_short(): void {
        $this->faq->create_question(['title' => 'ab']);

        $this->setUser($this->getDataGenerator()->create_user());
        $result = $this->search('ab');

        $this->assertTrue($result['tooshort']);
        $this->assertSame(0, $result['total']);
    }

    /**
     * Editing webservices work for editors.
     */
    public function test_edit_functions(): void {
        $first = $this->faq->create_question(['sortorder' => 2]);
        $second = $this->faq->create_question(['sortorder' => 1]);

        $this->setAdminUser();

        $this->assertSame(['status' => 0], toggle_entry_visibility::execute((int)$first->id));
        $this->assertSame(['status' => true], set_wbfaq_order::execute([(int)$second->id, (int)$first->id]));
        $this->assertSame(
            [(int)$second->id, (int)$first->id],
            (new entry_repository())->get_sibling_ids(0, entry_repository::TYPE_QUESTION)
        );
        $this->assertSame(['status' => true], delete_entry::execute((int)$first->id));
        $this->assertNull((new entry_repository())->get((int)$first->id));
    }

    /**
     * Editing webservices need the capability.
     *
     * @dataProvider edit_functions_provider
     * @param callable $call
     */
    public function test_edit_functions_need_capability(callable $call): void {
        $question = $this->faq->create_question();

        $this->setUser($this->getDataGenerator()->create_user());

        $this->expectException(required_capability_exception::class);
        $call((int)$question->id);
    }

    /**
     * Returns the webservices for editing.
     *
     * @return array
     */
    public static function edit_functions_provider(): array {
        return [
            'delete' => [fn(int $id) => delete_entry::execute($id)],
            'toggle' => [fn(int $id) => toggle_entry_visibility::execute($id)],
            'order' => [fn(int $id) => set_wbfaq_order::execute([$id])],
        ];
    }

    /**
     * Functions are declared correctly, read functions are available without a session.
     */
    public function test_service_definitions(): void {
        global $CFG;

        $functions = [];
        require($CFG->dirroot . '/local/wb_faq/db/services.php');

        foreach (['get_category', 'get_entry', 'search'] as $name) {
            $this->assertSame('read', $functions["local_wb_faq_$name"]['type']);
            $this->assertFalse($functions["local_wb_faq_$name"]['loginrequired']);
        }
        foreach (['delete_entry', 'toggle_entry_visibility', 'set_wbfaq_order'] as $name) {
            $this->assertSame('write', $functions["local_wb_faq_$name"]['type']);
            $this->assertArrayNotHasKey('loginrequired', $functions["local_wb_faq_$name"]);
        }
    }
}
