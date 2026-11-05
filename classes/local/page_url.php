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
 * Deep links into the FAQ.
 *
 * @package    local_wb_faq
 * @copyright  2026 Wunderbyte GmbH <info@wunderbyte.at>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace local_wb_faq\local;

use moodle_url;

/**
 * Builds the links for a state of the FAQ, based on the page the FAQ is shown on.
 *
 * The parameters have a prefix, as the FAQ can be embedded into other pages by a shortcode.
 * The same names are used in amd/src/faq.js.
 *
 * @package    local_wb_faq
 * @copyright  2026 Wunderbyte GmbH <info@wunderbyte.at>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class page_url {
    /** @var string Parameter holding the category. */
    public const PARAM_CATEGORY = 'faqcat';

    /** @var string Parameter holding the opened question. */
    public const PARAM_ENTRY = 'faqentry';

    /** @var string Parameter holding the page number. */
    public const PARAM_PAGE = 'faqpage';

    /** @var string Parameter holding the search term. */
    public const PARAM_QUERY = 'faqq';

    /** @var moodle_url The page without any FAQ parameters. */
    private moodle_url $base;

    /**
     * Constructor.
     *
     * @param moodle_url $url The page the FAQ is displayed on.
     */
    public function __construct(moodle_url $url) {
        $this->base = new moodle_url($url);
        $this->base->remove_params(self::PARAM_CATEGORY, self::PARAM_ENTRY, self::PARAM_PAGE, self::PARAM_QUERY);
    }

    /**
     * Returns the page without any FAQ parameters.
     *
     * @return moodle_url
     */
    public function base(): moodle_url {
        return new moodle_url($this->base);
    }

    /**
     * Returns the link to a page of a category, optionally with an opened question.
     *
     * @param int $categoryid
     * @param int $page
     * @param int $entryid
     * @return string
     */
    public function category(int $categoryid, int $page = 0, int $entryid = 0): string {
        return $this->build([
            self::PARAM_CATEGORY => $categoryid,
            self::PARAM_PAGE => $page,
            self::PARAM_ENTRY => $entryid,
        ]);
    }

    /**
     * Returns the link to a page of a search.
     *
     * @param string $query
     * @param int $page
     * @return string
     */
    public function search(string $query, int $page = 0): string {
        return $this->build([self::PARAM_QUERY => $query, self::PARAM_PAGE => $page]);
    }

    /**
     * Builds a link, leaving out empty parameters.
     *
     * @param array $params
     * @return string
     */
    private function build(array $params): string {
        return (new moodle_url($this->base, array_filter($params)))->out(false);
    }
}
