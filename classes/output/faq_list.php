<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Contains class mod_questionnaire\output\indexpage
 *
 * @package    local_wb_faq
 * @copyright  2022 Wunderbyte Gmbh <info@wunderbyte.at>
 * @author     Georg Maißer
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

namespace local_wb_faq\output;

use context_system;
use local_wb_faq\form\categories;
use local_wb_faq\wb_faq;
use renderable;
use renderer_base;
use templatable;

/**
 * display faq list
 * @package local_wb_faq
 *
 */
class faq_list implements renderable, templatable {

    /**
     * data is the array used for output.
     *
     * @var array
     */
    private $data = [];

    /**
     * uid to identify search & list which belong together.
     *
     * @var null|string
     */
    private $uid = null;

    /**
     * Constructor.
     * @param integer $categoryid
     * @param string $uid
     */
    public function __construct($categoryid = 0, string $uid, $allowedit = false) {

        $this->uid = $uid;

        $context = context_system::instance();

        if (!has_capability('local/wb_faq:canedit', $context)) {
            $allowedit = false;
        }

        $sm = new wb_faq();
        $allfaqs = $sm->load_from_cache(true, $categoryid, $allowedit);

        $data = [];

        $data['json'] = json_encode($allfaqs, true);
        $data['root'] = $categoryid;
        $data['allowedit'] = $allowedit;

        $this->data = $data;
    }

    /**
     * Prepare data for use in a template
     *
     * @param renderer_base $output
     * @return array
     */
    public function export_for_template(renderer_base $output) {
        $data = $this->data;
        $data['uid'] = $this->uid;

        $context = context_system::instance();

        if ($data['allowedit'] && has_capability('local/wb_faq:canedit', $context)) {
            $data['canedit'] = true;
        }

        return $data;
    }
}
