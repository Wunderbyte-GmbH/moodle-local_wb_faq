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
 * Tests of the output and of the integrations.
 *
 * @package    local_wb_faq
 * @copyright  2026 Wunderbyte GmbH <info@wunderbyte.at>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace local_wb_faq;

use advanced_testcase;
use local_wb_faq\local\page_url;
use local_wb_faq\output\faq_list;
use local_wb_faq\output\pagination;
use local_wb_faq\search\faqentry;
use core_search\manager;
use moodle_url;

/**
 * Tests of the output and of the integrations.
 *
 * @package    local_wb_faq
 * @copyright  2026 Wunderbyte GmbH <info@wunderbyte.at>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \local_wb_faq\output\faq_list
 * @covers     \local_wb_faq\output\pagination
 * @covers     \local_wb_faq\local\page_url
 * @covers     \local_wb_faq\shortcodes
 * @covers     \local_wb_faq\search\faqentry
 */
final class output_test extends advanced_testcase {
    /** @var \local_wb_faq_generator */
    private $faq;

    /**
     * Prepares a test.
     */
    protected function setUp(): void {
        global $PAGE;

        parent::setUp();
        $this->resetAfterTest();
        $this->faq = $this->getDataGenerator()->get_plugin_generator('local_wb_faq');
        $PAGE->set_url(new moodle_url('/local/wb_faq/index.php'));
        $PAGE->set_context(\context_system::instance());
    }

    /**
     * Renders an instance.
     *
     * @param array $options
     * @param int $rootid
     * @return string
     */
    private function render(array $options = [], int $rootid = 0): string {
        global $PAGE;

        $faq = new faq_list(new moodle_url('/local/wb_faq/index.php'), 'test', $rootid, $options);

        return $PAGE->get_renderer('local_wb_faq')->render($faq);
    }

    /**
     * Issue #2: The buttons to add the first entry are part of the page itself.
     */
    public function test_empty_faq_offers_add_buttons(): void {
        $this->setAdminUser();
        $html = $this->render(['allowedit' => true]);

        $this->assertStringContainsString(get_string('addcategory', 'local_wb_faq'), $html);
        $this->assertStringContainsString(get_string('addquestion', 'local_wb_faq'), $html);
    }

    /**
     * Normal users do not get edit buttons, even if the page allows editing.
     */
    public function test_normal_users_get_no_add_buttons(): void {
        $this->setUser($this->getDataGenerator()->create_user());
        $html = $this->render(['allowedit' => true]);

        $this->assertStringNotContainsString(get_string('addcategory', 'local_wb_faq'), $html);
    }

    /**
     * Deep links get rendered by the server.
     */
    public function test_requested_state_is_rendered(): void {
        $category = $this->faq->create_category(['title' => 'Category']);
        $question = $this->faq->create_question([
            'title' => 'Question',
            'parentid' => $category->id,
            'content' => '<p>Open answer</p>',
        ]);

        $this->setUser($this->getDataGenerator()->create_user());

        $html = $this->render(['categoryid' => (int)$category->id]);
        $this->assertStringContainsString('Question', $html);
        $this->assertStringNotContainsString('Open answer', $html);

        $this->assertStringContainsString('Open answer', $this->render(['entryid' => (int)$question->id]));
    }

    /**
     * Searches get rendered by the server.
     */
    public function test_search_is_rendered(): void {
        $this->faq->create_question(['title' => 'Findable']);
        $this->faq->create_question(['title' => 'Other']);

        $this->setUser($this->getDataGenerator()->create_user());
        $html = $this->render(['query' => 'findable']);

        $this->assertStringContainsString('Findable', $html);
        $this->assertStringNotContainsString('Other', $html);
    }

    /**
     * The breadcrumb labels can be replaced by a setting.
     */
    public function test_mapstrings(): void {
        $category = $this->faq->create_category(['title' => 'Original']);
        set_config('mapstrings', '{"Original": "Replaced"}', 'local_wb_faq');

        $this->setUser($this->getDataGenerator()->create_user());

        $this->assertStringContainsString('Replaced', $this->render(['categoryid' => (int)$category->id]));

        set_config('mapstrings', 'invalid json', 'local_wb_faq');
        $this->assertStringContainsString('Original', $this->render(['categoryid' => (int)$category->id]));
    }

    /**
     * The design comes from the setting, and unknown values fall back to the first one.
     */
    public function test_design(): void {
        $this->setUser($this->getDataGenerator()->create_user());

        $this->assertStringContainsString('wb-faq-design-classic', $this->render());

        set_config('design', 'modern', 'local_wb_faq');
        $this->assertStringContainsString('wb-faq-design-modern', $this->render());

        set_config('design', 'unknown"><script>', 'local_wb_faq');
        $this->assertSame('classic', faq_list::get_design());
    }

    /**
     * The page numbers skip what is too far away from the current page.
     */
    public function test_pagination(): void {
        $urlbuilder = fn(int $page): string => "https://example.com/?page=$page";

        $this->assertFalse(pagination::export(3, 0, 10, $urlbuilder)['haspages']);

        $result = pagination::export(100, 5, 10, $urlbuilder);
        $labels = array_map(fn($page) => $page['ellipsis'] ? '...' : $page['label'], $result['pages']);

        $this->assertSame([1, '...', 4, 5, 6, 7, 8, '...', 10], $labels);
        $this->assertSame(4, $result['previouspage']);
        $this->assertSame(6, $result['nextpage']);
        $this->assertSame(0, pagination::clamp_page(5, 0, 10));
    }

    /**
     * Links keep the other parameters of the page, and replace the old FAQ parameters.
     */
    public function test_page_url(): void {
        $urls = new page_url(new moodle_url('/mod/page/view.php', ['id' => 5, 'faqcat' => 7, 'faqq' => 'old']));

        $this->assertStringEndsWith('/mod/page/view.php?id=5', $urls->base()->out(false));
        $this->assertStringEndsWith('/mod/page/view.php?id=5&faqcat=3&faqpage=2', $urls->category(3, 2));
        $this->assertStringEndsWith('/mod/page/view.php?id=5&faqq=find%20me', $urls->search('find me'));
    }

    /**
     * The shortcode shows a subtree, or explains what is wrong.
     */
    public function test_shortcode(): void {
        $this->faq->create_category(['title' => 'Other']);
        $root = $this->faq->create_category(['title' => 'Root']);
        $this->faq->create_category(['title' => 'Child', 'parentid' => $root->id]);

        $this->setUser($this->getDataGenerator()->create_user());
        $next = fn() => '';

        $html = shortcodes::show_faq('faq', ['cat' => 'Root'], null, null, $next);
        $this->assertStringContainsString('Child', $html);
        $this->assertStringNotContainsString('Other', $html);

        $this->assertStringContainsString(
            get_string('categorynotfound', 'local_wb_faq', 'Missing'),
            shortcodes::show_faq('faq', ['cat' => 'Missing'], null, null, $next)
        );

        $this->assertStringNotContainsString('faq-search-form', shortcodes::show_faq('faq', ['nosearch' => 1], null, null, $next));
        $this->assertStringNotContainsString('Other', shortcodes::show_faq('faq', ['nolist' => 1], null, null, $next));

        $this->setUser(0);
        $this->assertSame(
            get_string('loginrequired', 'local_wb_faq'),
            shortcodes::show_faq('faq', [], null, null, $next)
        );
    }

    /**
     * The help menu shows the configured links only, and invalid lines are skipped.
     */
    public function test_navbar(): void {
        global $CFG, $PAGE;

        require_once($CFG->dirroot . '/local/wb_faq/lib.php');
        $renderer = $PAGE->get_renderer('core');

        $this->setUser($this->getDataGenerator()->create_user());
        $this->assertSame('', local_wb_faq_render_navbar_output($renderer));

        set_config('usesupport', 1, 'local_wb_faq');
        set_config('supportlinks', "Contact|https://example.com/contact\ninvalid line\nBad|javascript:alert(1)", 'local_wb_faq');

        $html = local_wb_faq_render_navbar_output($renderer);
        $this->assertStringContainsString('https://example.com/contact', $html);
        $this->assertStringNotContainsString('javascript:', $html);
        $this->assertStringNotContainsString('invalid line', $html);

        $this->setGuestUser();
        $this->assertSame('', local_wb_faq_render_navbar_output($renderer));
    }

    /**
     * The global search respects the same rules as the FAQ.
     */
    public function test_global_search_access(): void {
        $course = $this->getDataGenerator()->create_course();
        $public = $this->faq->create_question();
        $hidden = $this->faq->create_question(['enabled' => 0]);
        $restricted = $this->faq->create_category(['courseid' => $course->id]);

        $this->setUser($this->getDataGenerator()->create_user());
        $area = new faqentry();

        $this->assertSame(manager::ACCESS_GRANTED, $area->check_access($public->id));
        $this->assertSame(manager::ACCESS_DENIED, $area->check_access($hidden->id));
        $this->assertSame(manager::ACCESS_DENIED, $area->check_access($restricted->id));
        $this->assertSame(manager::ACCESS_DELETED, $area->check_access(999999));

        $document = $area->get_document($public);
        $this->assertSame((int)SITEID, (int)$document->get('courseid'));
    }
}
