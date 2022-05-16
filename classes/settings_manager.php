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
 * local wb_faq
 *
 * @package     local_wb_faq
 * @author      Thomas Winkler
 * @copyright   2022 Wunderbyte GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_wb_faq;

use local_wb_faq_external;
use stdClass;

defined('MOODLE_INTERNAL') || die;

/**
 * Class settings_manager
 * @author      Thomas Winkler
 * @copyright   2022 Wunderbyte GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class settings_manager {

    private $id;

    private $title;

    private $content;

    private $type;

    private $parentid;

    private $sortorder;

    /**
     * entity constructor.
     *
     */
    public function __construct(int $id = null) {
        global $DB;
        if ($id == null) {
            $this->id = $id;
            $this->data = new stdClass();
            $this->data->id = $this->id;
        } else {
            $this->data = $DB->get_record('local_wb_faq_entry', array('id' => $id));
            $this->id = $this->data->id;
            $this->title = $this->data->title;
        }
    }

    /**
     *
     * This is to create a new entity in the database
     *
     * @param stdClass $data
     *
     */
    public function create_faq(stdClass $data): int {
        global $DB;
        $temp = $data->content['text'];
        unset($data->content);
        $data->content = $temp;
        $id = $DB->insert_record('local_wb_faq_entry', $data);
        return $id;
    }

    /**
     *
     * This is to update the entity based on the data object
     *
     * @param stdClass $data
     * @return int
     */
    public function update_faq(stdClass $data): int {
        global $DB;
        $temp = $data->content['text'];
        unset($data->content);
        $data->content = $temp;
        return $DB->update_record('local_wb_faq_entry', $data);
    }

    /**
     * Prepare submitted form data for writing to db.
     *
     * @param stdClass $formdata
     * @return stdClass
     */
    public static function form_to_db(stdClass $formdata): stdClass {
        $record = new stdClass();
        $record->id = isset($formdata->id) ? $formdata->id : 0;
        $record->dataid = $formdata->d;
        $record->name = $formdata->name;
        $record->description = $formdata->description;
        $record->required = $formdata->required;
        return $record;
    }

    /**
     * Given a db record make it ready for the form.
     *
     * @param stdClass $record
     * @return stdClass
     */
    public static function db_to_form(stdClass $record): stdClass {
        $formdata = new stdClass();
        $formdata->id = isset($record->id) ? $record->id : 0;
        $formdata->name = $record->name;
        $formdata->description['text'] = $record->description;
        $formdata->name = $record->name;
        $formdata->id = $record->id;
        $formdata->parentid = $record->parentid;
        $formdata->sortorder  = $record->sortorder;
        $formdata->type  = $record->type;
        return $formdata;
    }

    /**
     *
     * This is to update or delete an entity if it does not exist
     *
     * @return mixed
     */
    public function delete() {
        global $DB;
        $DB->delete_records('local_wb_faq_entry', array('id' => $this->id));
    }
}
