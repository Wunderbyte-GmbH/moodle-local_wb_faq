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
 * A complete FAQ instance.
 *
 * @package    local_wb_faq
 * @author     Georg Maißer
 * @copyright  2022 Wunderbyte GmbH <info@wunderbyte.at>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace local_wb_faq\output;

use local_wb_faq\local\access;
use local_wb_faq\local\page_url;
use moodle_url;
use renderable;
use renderer_base;
use templatable;

/**
 * Search form and content of a FAQ instance, already rendered for the current state.
 * This makes deep links work, and the FAQ of an empty site has its edit buttons right away.
 *
 * @package    local_wb_faq
 * @author     Georg Maißer
 * @copyright  2022 Wunderbyte GmbH <info@wunderbyte.at>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class faq_list implements renderable, templatable {
    /** @var string[] The available designs. Each one has rules in styles.css and a string design_<name>. */
    public const DESIGNS = ['classic', 'modern'];

    /** @var access */
    private access $access;

    /** @var page_url */
    private page_url $urls;

    /** @var string */
    private string $uid;

    /** @var int */
    private int $rootid;

    /** @var array */
    private array $options;

    /**
     * Constructor.
     *
     * @param moodle_url $pageurl The page the FAQ is displayed on.
     * @param string $uid Identifies the instance on the page.
     * @param int $rootid The topmost category of this instance.
     * @param array $options Optional keys: categoryid, entryid, page, query, allowedit, showsearch, showlist, editurl.
     */
    public function __construct(moodle_url $pageurl, string $uid, int $rootid = 0, array $options = []) {
        $this->access = new access();
        $this->urls = new page_url($pageurl);
        $this->uid = $uid;
        $this->rootid = $rootid;
        $this->options = $options + [
            'categoryid' => $rootid,
            'entryid' => 0,
            'page' => 0,
            'query' => '',
            'allowedit' => false,
            'showsearch' => true,
            'showlist' => true,
            'editurl' => null,
        ];
    }

    /**
     * Creates the instance for a page of this plugin from the parameters of the request.
     *
     * @param moodle_url $pageurl
     * @param bool $allowedit
     * @param moodle_url|null $editurl Link to the editing page, shown to users who can edit.
     * @return self
     */
    public static function from_request(moodle_url $pageurl, bool $allowedit, ?moodle_url $editurl = null): self {
        return new self($pageurl, uniqid(), 0, [
            'categoryid' => optional_param(page_url::PARAM_CATEGORY, 0, PARAM_INT),
            'entryid' => optional_param(page_url::PARAM_ENTRY, 0, PARAM_INT),
            'page' => optional_param(page_url::PARAM_PAGE, 0, PARAM_INT),
            'query' => optional_param(page_url::PARAM_QUERY, '', PARAM_TEXT),
            'allowedit' => $allowedit,
            'editurl' => $editurl,
        ]);
    }

    /**
     * Returns the configured design.
     *
     * @return string
     */
    public static function get_design(): string {
        $design = (string)get_config('local_wb_faq', 'design');

        return in_array($design, self::DESIGNS, true) ? $design : self::DESIGNS[0];
    }

    /**
     * Returns the data for the template local_wb_faq/faq_list.
     *
     * @param renderer_base $output
     * @return array
     */
    public function export_for_template(renderer_base $output): array {
        $query = trim((string)$this->options['query']);
        $canedit = $this->options['allowedit'] && $this->access->can_edit();
        $base = $this->urls->base();

        $hiddenparams = [];
        foreach ($base->params() as $name => $value) {
            $hiddenparams[] = ['name' => $name, 'value' => $value];
        }

        $data = [
            'uid' => $this->uid,
            'design' => self::get_design(),
            'root' => $this->rootid,
            'query' => $query,
            'allowedit' => $canedit,
            'nologin' => !isloggedin(),
            'pageurl' => $this->urls->base()->out(false),
            'formaction' => $base->out_omit_querystring(),
            'hiddenparams' => $hiddenparams,
            'minlength' => search_view::MIN_QUERY_LENGTH,
            'showsearch' => $this->options['showsearch'],
            'history' => true,
            'categoryid' => $this->rootid,
            'entryid' => 0,
            'page' => 0,
        ];

        if ($this->access->can_edit() && $this->options['editurl']) {
            $data['editurl'] = $this->options['editurl']->out(false);
        }

        if ($query !== '') {
            $view = new search_view($this->access, $this->urls, $this->uid, $this->rootid, $query, (int)$this->options['page']);
            $data['search'] = $view->export_for_template($output);
            $data['page'] = $data['search']['page'];
        } else if ($this->options['showlist']) {
            $view = new category_view(
                $this->access,
                $this->urls,
                $this->uid,
                $this->rootid,
                (int)$this->options['categoryid'],
                (int)$this->options['entryid'],
                (int)$this->options['page'],
                $canedit
            );
            $data['category'] = $view->export_for_template($output);
            $data['categoryid'] = $data['category']['categoryid'];
            $data['entryid'] = $data['category']['entryid'];
            $data['page'] = $data['category']['page'];
        }

        return $data;
    }
}
