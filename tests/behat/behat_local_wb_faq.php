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
 * Behat steps of the plugin.
 *
 * @package    local_wb_faq
 * @copyright  2026 Wunderbyte GmbH <info@wunderbyte.at>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../lib/behat/behat_base.php');

/**
 * Steps to prove that the FAQ changes its content without reloading the page.
 *
 * @package    local_wb_faq
 * @copyright  2026 Wunderbyte GmbH <info@wunderbyte.at>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_local_wb_faq extends behat_base {
    /**
     * Sets a marker in the window. A reload of the page would remove it.
     *
     * @Given /^I set the FAQ page reload marker$/
     * @return void
     */
    public function i_set_the_faq_page_reload_marker(): void {
        $this->getSession()->executeScript('window.localWbFaqMarker = true;');
    }

    /**
     * Fails if the page was reloaded since the marker was set.
     *
     * @Then /^the FAQ page should not have been reloaded$/
     * @return void
     */
    public function the_faq_page_should_not_have_been_reloaded(): void {
        if (!$this->evaluate_script('return window.localWbFaqMarker === true;')) {
            throw new \Behat\Mink\Exception\ExpectationException('The page was reloaded.', $this->getSession());
        }
    }

    /**
     * Fails if the url of the page does not contain the text.
     *
     * @Then /^the FAQ page url should contain "(?P<text>[^"]*)"$/
     * @param string $text
     * @return void
     */
    public function the_faq_page_url_should_contain(string $text): void {
        $url = $this->getSession()->getCurrentUrl();
        if (strpos($url, $text) === false) {
            throw new \Behat\Mink\Exception\ExpectationException("'$text' not found in $url", $this->getSession());
        }
    }

    /**
     * Opens a FAQ page with a deep link to a category.
     *
     * @Given /^I open the FAQ deep link to category "(?P<title>[^"]*)"$/
     * @param string $title
     * @return void
     */
    public function i_open_the_faq_deep_link_to_category(string $title): void {
        global $DB;

        $id = $DB->get_field('local_wb_faq_entry', 'id', ['title' => $title, 'type' => '0'], MUST_EXIST);
        $this->getSession()->visit($this->locate_path('/local/wb_faq/index.php?faqcat=' . $id));
    }

    /**
     * Opens a FAQ page with a deep link to a question.
     *
     * @Given /^I open the FAQ deep link to question "(?P<title>[^"]*)"$/
     * @param string $title
     * @return void
     */
    public function i_open_the_faq_deep_link_to_question(string $title): void {
        global $DB;

        $id = $DB->get_field('local_wb_faq_entry', 'id', ['title' => $title, 'type' => '1'], MUST_EXIST);
        $this->getSession()->visit($this->locate_path('/local/wb_faq/index.php?faqentry=' . $id));
    }

    /**
     * Uses the back button of the browser.
     *
     * @When /^I go back in the FAQ browser history$/
     * @return void
     */
    public function i_go_back_in_the_faq_browser_history(): void {
        $this->getSession()->back();
        $this->wait_for_pending_js();
    }

    /**
     * Opens a page of the plugin.
     *
     * @Given /^I open the FAQ page "(?P<page>index|wb_faq)"$/
     * @param string $page
     * @return void
     */
    public function i_open_the_faq_page(string $page): void {
        $this->getSession()->visit($this->locate_path("/local/wb_faq/$page.php"));
    }
}
