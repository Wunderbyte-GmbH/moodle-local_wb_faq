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
 * Search area of the plugin.
 *
 * @package    local_wb_faq
 * @copyright  2022 Wunderbyte GmbH <info@wunderbyte.at>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace local_wb_faq\search;

use context;
use context_system;
use core_search\base;
use core_search\document;
use core_search\document_factory;
use core_search\manager;
use local_wb_faq\local\access;
use local_wb_faq\local\entry_repository;
use moodle_recordset;
use moodle_url;
use stdClass;

/**
 * Makes the FAQ findable by the global search.
 *
 * @package    local_wb_faq
 * @copyright  2022 Wunderbyte GmbH <info@wunderbyte.at>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class faqentry extends base {
    /**
     * Returns the recordset of the records to index.
     *
     * @param int $modifiedfrom
     * @param context|null $context
     * @return moodle_recordset|null
     */
    public function get_document_recordset($modifiedfrom = 0, ?context $context = null) {
        global $DB;

        return $DB->get_recordset_select(
            entry_repository::TABLE,
            'timemodified >= ? AND enabled = 1',
            [$modifiedfrom],
            'timemodified ASC'
        );
    }

    /**
     * Returns the document of a record.
     *
     * @param stdClass $record
     * @param array $options
     * @return document
     */
    public function get_document($record, $options = []) {
        $doc = document_factory::instance($record->id, $this->componentname, $this->areaname);
        $doc->set('title', content_to_text($record->title, FORMAT_PLAIN));
        $doc->set('content', content_to_text((string)$record->content, FORMAT_HTML));
        $doc->set('contextid', context_system::instance()->id);
        $doc->set('courseid', SITEID);
        $doc->set('owneruserid', manager::NO_OWNER_ID);
        $doc->set('modified', $record->timemodified);

        if (isset($options['lastindexedtime']) && $options['lastindexedtime'] < $record->timecreated) {
            $doc->set_is_new(true);
        }

        return $doc;
    }

    /**
     * Whether the current user can access a document.
     *
     * @param int $id
     * @return int
     */
    public function check_access($id) {
        $record = (new entry_repository())->get((int)$id);
        if (!$record) {
            return manager::ACCESS_DELETED;
        }

        return (new access())->can_view_record($record) ? manager::ACCESS_GRANTED : manager::ACCESS_DENIED;
    }

    /**
     * Returns the link to a document.
     *
     * @param document $doc
     * @return moodle_url
     */
    public function get_doc_url(document $doc) {
        return new moodle_url('/local/wb_faq/view.php', ['id' => $doc->get('itemid')]);
    }

    /**
     * Returns the link to the context of a document.
     *
     * @param document $doc
     * @return moodle_url
     */
    public function get_context_url(document $doc) {
        return $this->get_doc_url($doc);
    }
}
