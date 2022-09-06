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
 * FAQ search
 *
 * @package    local_wb_faq
 * @copyright  2022 Thomas Winkler
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_wb_faq\search;

use local_wb_faq\settings_manager;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/forum/lib.php');

/**
 * Forum posts search area.
 *
 * @package    mod_forum
 * @copyright  2015 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class faqentry extends \core_search\base {

    /**
     * @var array Internal quick static cache.
     */
    protected $forumsdata = array();


    /**
     * Returns recordset containing required data for indexing forum posts.
     *
     * @param int $modifiedfrom timestamp
     * @param \context|null $context Optional context to restrict scope of returned results
     * @return moodle_recordset|null Recordset (or null if no results)
     */
    public function get_document_recordset($modifiedfrom = 0, \context $context = null) {
        global $DB;
        $sql = "SELECT * FROM {local_wb_faq_entry} 
                 WHERE timemodified >= ? ORDER BY timemodified ASC";
        return $DB->get_recordset_sql($sql, [$modifiedfrom]);
    }

    /**
     * Returns the document associated with this post id.
     *
     * @param stdClass $record Post info.
     * @param array    $options
     * @return \core_search\document
     */
    public function get_document($record, $options = array()) {

        $context = \context_system::instance();
        // Prepare associative array with data from DB.
        $doc = \core_search\document_factory::instance($record->id, $this->componentname, $this->areaname);
        $doc->set('title', content_to_text($record->title, false));
        $doc->set('content', content_to_text($record->content, FORMAT_HTML));
        $doc->set('contextid', $context->id);
        // Not associated with a course
        $courseid = settings_manager::get_related_courseid_from_entryid($record->id);
        $doc->set('courseid', $courseid);
        $doc->set('owneruserid', \core_search\manager::NO_OWNER_ID);
        $doc->set('modified', $record->timemodified);

        // Check if this document should be considered new.
        if (isset($options['lastindexedtime']) && ($options['lastindexedtime'] < $record->timecreated)) {
            // If the document was created after the last index time, it must be new.
            $doc->set_is_new(true);
        }

        return $doc;
    }

    /**
     * Returns true if this area uses file indexing.
     *
     * @return bool
     */
    public function uses_file_indexing() {
        return false;
    }


    /**
     * Whether the user can access the document or not.
     *
     * @throws \dml_missing_record_exception
     * @throws \dml_exception
     * @param int $id Forum post id
     * @return bool
     */
    public function check_access($id) {
        global $USER;
        return \core_search\manager::ACCESS_GRANTED;
    }

    /**
     * Link to the forum post discussion
     *
     * @param \core_search\document $doc
     * @return \moodle_url
     */
    public function get_doc_url(\core_search\document $doc) {
        // The post is already in static cache, we fetch it in self::search_access.
        //$post = $this->get_post($doc->get('itemid'));
        return new \moodle_url('/local/wb_faq/view.php', array('id' => $doc->get('itemid')));
    }

    /**
     * Link to the FAQ.
     *
     * @param \core_search\document $doc
     * @return \moodle_url
     */
    public function get_context_url(\core_search\document $doc) {
        return new \moodle_url('/local/wb_faq/view.php', array('id' => $doc->get('itemid')));
    }


    /**
     * Confirms that data entries support group restrictions.
     *
     * @return bool false
     */
    public function supports_group_restriction() {
        return false;
    }
}
