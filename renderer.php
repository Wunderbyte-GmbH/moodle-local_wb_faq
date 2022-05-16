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
 * Local wb_faq Renderer
 *
 * @package     local_wb_faq
 * @author      Thomas Winkler
 * @copyright   2022 Wunderbyte GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 *
 * Class local_wb_faq_renderer
 *
 * @copyright   2022 Wunderbyte GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_wb_faq_renderer extends plugin_renderer_base
{   public $records = [];

    public function get_submenuitem($parent, $name) {
        global $DB, $CFG, $USER;
        $html = '';
        $records = \local_wb_faq\wb_faq::list_all_subwb_faq($parent);
        if ($records) {
            $html .= "<li class='list-group-item'>";
            $html .= '<div class="pull-right">' .
                '<a href="' . new moodle_url($CFG->wwwroot . '/local/wb_faq/view.php',
                    array('id' => $parent)) . '" class="btn btn--plain btn--smaller btn--primary btn_edit">' .
                    '<i class="fa fa-edit"></i>' .
                get_string('view', 'local_wb_faq') . '</a> | ' .
                '<a href="' . new moodle_url($CFG->wwwroot . '/local/wb_faq/edit.php',
                    array('id' => $parent)) . '" class="btn btn--plain btn--smaller btn--primary btn_edit">' .
                    '<i class="fa fa-edit"></i>' .
                get_string('edit', 'local_wb_faq') . '</a> | ' .
                '<a href="' . new moodle_url($CFG->wwwroot . '/local/wb_faq/wb_faq.php',
                    array('del' => $parent, 'sesskey' => $USER->sesskey)) .
                    '" class="btn btn--plain btn--smaller btn--primary btn_edit">' .
                    '<i class="fa fa fa-trash"></i>' .
                    get_string('delete', 'local_wb_faq') . ' </a></div>';
            $html .= "<h4 class=''>" . $name . "</h4>";
            $html .= "<ul class='pl-4 border-0'>";
            foreach ($records as $entity) {
                $html .= $this->get_submenuitem($entity->id, $entity->name);
            }
            $html .= "</ul>";
            $html .= "</li>";
        } else {
            $html .= "<li class='list-group-item'>";
            $html .= '<div class="pull-right">' .
                '<a href="' . new moodle_url($CFG->wwwroot . '/local/wb_faq/view.php',
                    array('id' => $parent)) . '" class="btn btn--plain btn--smaller btn--primary btn_edit">' .
                    '<i class="fa fa fa-edit"></i>' .
                get_string('view', 'local_wb_faq') . '</a> | ' .
                '<a href="' . new moodle_url($CFG->wwwroot . '/local/wb_faq/edit.php',
                    array('id' => $parent)) . '" class="btn btn--plain btn--smaller btn--primary btn_edit">' .
                    '<i class="fa fa fa-edit"></i>' .
                get_string('edit', 'local_wb_faq') . '</a> | ' .
                '<a href="' . new moodle_url($CFG->wwwroot . '/local/wb_faq/wb_faq.php',
                    array('del' => $parent, 'sesskey' => $USER->sesskey)) .
                    '" class="btn btn--plain btn--smaller btn--primary btn_edit">' .
                    '<i class="fa fa fa-trash"></i>' .
                get_string('delete', 'local_wb_faq') . ' </a></div>';
            $html .= "<h4 class=''>" . $name . "</h4>";
            $html .= "</li>";
        }
        return $html;
    }

    public function list_wb_faq() {
        global $DB, $CFG;

        $html = '<ul class="list-group mb-4">';
        $html .= '<li class="list-group-item bg-light"><h4>Entity List</h4></li>';
        $html .= "<li class='list-group-item'><a href='"
        . new moodle_url($CFG->wwwroot . '/local/wb_faq/edit.php') .
           "' class='btn btn-smaller btn-primary pull-right mx-2'>" .
           '<i class="fa fa-plus"></i> ' .
            get_string("addentity", "local_wb_faq") . "</a><a href='"
            . new moodle_url($CFG->wwwroot . '/local/wb_faq/customfield.php') .
               "' class='btn btn-smaller btn-primary pull-right'>" .
               '<i class="fa fa-plus"></i> ' .
                get_string("addcategory", "local_wb_faq") . "</a></li>";
        $records = \local_wb_faq\wb_faq::list_all_parent_wb_faq();
        foreach ($records as $entity) {
            $html .= $this->get_submenuitem($entity->id, $entity->name);
        }

        $html .= "<li class='list-group-item'><a href='"
         . new moodle_url($CFG->wwwroot . '/local/wb_faq/edit.php') .
            "' class='btn btn-smaller btn-primary pull-right mx-2'>" .
            '<i class="fa fa-plus"></i> ' .
             get_string("addentity", "local_wb_faq") . "</a><a href='"
             . new moodle_url($CFG->wwwroot . '/local/wb_faq/customfield.php') .
                "' class='btn btn-smaller btn-primary pull-right'>" .
                '<i class="fa fa-plus"></i> ' .
                 get_string("addcategory", "local_wb_faq") . "</a></li>";

        $html .= "</ul>";
        return $html;
    }


    public function get_submenuitem_select($parent, $name) {
        global $DB, $CFG, $USER;
        $html = '';
        $records = \local_wb_faq\wb_faq::list_all_subwb_faq($parent);
        if ($records) {
            $html .= "<li  class='list-group-item p-0 pl-2'>";

            $html .= "<span class='' href='#parent-". $parent
            ."' data-toggle='collapse' aria-expanded='false'>" . $name . "</span>";
            $html .= "<div class='pull-right'><span class='btn btn-primary py-0 fa-plus fa' data-action='addentity'
            data-entityname='" .$name. "'  data-entityid='" .$parent. "'></span></div>";

            $html .= "<ul class='pl-4 border-0 collapse' id='parent-".$parent."'>";
            foreach ($records as $entity) {
                $html .= $this->get_submenuitem_select($entity->id, $entity->name);
            }
            $html .= "</ul>";
            $html .= "</li>";
        } else {
            $html .= "<li class='list-group-item p-0 pl-2'>";
            $html .= "<span class=''>" . $name . "</span>";
            $html .= "<div class='pull-right'><span class='btn btn-primary py-0 fa-plus fa' data-action='addentity'
            data-entityname='" .$name. "'  data-entityid='" .$parent. "'></span></div>";
            $html .= "</li>";
        }
        return $html;
    }

    public function list_wb_faq_select() {
        global $DB, $CFG;
        $html = '<ul class="list-group group-root my-4">';
        $records = \local_wb_faq\wb_faq::list_all_parent_wb_faq();
        foreach ($records as $entity) {
            $html .= $this->get_submenuitem_select($entity->id, $entity->name);
        }
        $html .= "</ul>";
        return $html;
    }
}

