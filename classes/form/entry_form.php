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
 * Base class of the edit forms.
 *
 * @package    local_wb_faq
 * @copyright  2022 Wunderbyte GmbH <info@wunderbyte.at>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace local_wb_faq\form;

use context;
use context_system;
use core_form\dynamic_form;
use local_wb_faq\local\entry_repository;
use local_wb_faq\wb_faq;
use moodle_url;
use stdClass;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/formslib.php');

/**
 * Everything the forms for categories and questions have in common.
 *
 * @package    local_wb_faq
 * @copyright  2022 Wunderbyte GmbH <info@wunderbyte.at>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class entry_form extends dynamic_form {
    /**
     * Get context for dynamic submission.
     *
     * @return context
     */
    protected function get_context_for_dynamic_submission(): context {
        return context_system::instance();
    }

    /**
     * Check access for dynamic submission.
     *
     * @return void
     */
    protected function check_access_for_dynamic_submission(): void {
        require_capability('local/wb_faq:canedit', context_system::instance());
    }

    /**
     * Saves the submitted data.
     *
     * @return array The id of the saved record.
     */
    public function process_dynamic_submission(): array {
        $data = $this->get_data();
        $data->id = (int)$data->id;

        if ($data->id) {
            wb_faq::update_entry($data);
        } else {
            $data->id = wb_faq::create_entry($data);
        }

        return ['id' => $data->id];
    }

    /**
     * Get page URL for dynamic submission.
     *
     * @return moodle_url
     */
    protected function get_page_url_for_dynamic_submission(): moodle_url {
        return new moodle_url('/local/wb_faq/wb_faq.php');
    }

    /**
     * Returns the record being edited, or a new one inside the category given to the form.
     *
     * @return stdClass
     */
    protected function get_record_for_form(): stdClass {
        $id = (int)$this->optional_param('id', 0, PARAM_INT);
        $record = $id ? (new entry_repository())->get($id) : null;

        return $record ?? (object)[
            'id' => 0,
            'parentid' => (int)$this->optional_param('parentid', 0, PARAM_INT),
            'enabled' => 1,
        ];
    }

    /**
     * Adds the fields used by both forms.
     *
     * @param string $type
     * @param string $titlestring
     * @return void
     */
    protected function add_common_fields(string $type, string $titlestring): void {
        $mform = $this->_form;

        $mform->addElement('hidden', 'id', 0);
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'type', $type);
        $mform->setType('type', PARAM_ALPHANUM);

        $mform->addElement('text', 'title', get_string($titlestring, 'local_wb_faq'), ['size' => 60]);
        $mform->setType('title', PARAM_TEXT);
        $mform->addRule('title', get_string('required'), 'required', null, 'client');
        $mform->addRule('title', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        $mform->addElement('advcheckbox', 'enabled', get_string('entryvisible', 'local_wb_faq'));
        $mform->setDefault('enabled', 1);

        $mform->addElement('select', 'parentid', get_string('input:parentid', 'local_wb_faq'), $this->get_parent_options());
        $mform->setType('parentid', PARAM_INT);
    }

    /**
     * Returns the possible parents as an indented list.
     *
     * @return array
     */
    private function get_parent_options(): array {
        $categories = (new entry_repository())->get_all_categories();

        return [0 => get_string('root', 'local_wb_faq')] + $this->get_children_options($categories, 0, 0);
    }

    /**
     * Returns the options of a level of the tree.
     *
     * @param stdClass[] $categories
     * @param int $parentid
     * @param int $depth
     * @return array
     */
    private function get_children_options(array $categories, int $parentid, int $depth): array {
        $options = [];
        foreach ($categories as $category) {
            if ((int)$category->parentid === $parentid) {
                $options[(int)$category->id] = str_repeat('- ', $depth) . format_string($category->title);
                $options += $this->get_children_options($categories, (int)$category->id, $depth + 1);
            }
        }

        return $options;
    }
}
