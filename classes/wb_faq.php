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

use cache_helper;
use context_system;
use stdClass;

/**
 * Class wb_faq
 * @author      Thomas Winkler
 * @copyright   2022 Wunderbyte GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . '/filelib.php');
class wb_faq {

    public $courselist;

    /**
     *
     * @var mixed
     */
    private $id;

    /**
     *
     * @var mixed
     */
    private $data;

    /**
     *
     * @var mixed
     */
    private $title;

    /**
     * Constructor.
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
     * Updates the cache
     * @param boolean $allowedit
     * @return void
     */
    private function update_cache($allowedit = false) {
        $cache = \cache::make('local_wb_faq', 'faqcache');
        $cachekey = $allowedit ? 'faq_cache_edit' : 'faq_cache';
        $cachedrawdata = $this->load_tree($allowedit);
        $cache->set($cachekey, $cachedrawdata);
    }

    /**
     * Builds the tree of the navigatin.
     *
     * @param stdClass $elements
     * @param integer $parentid
     * @param integer $depth
     * @return array
     */
    public function buildtree($elements, $parentid = 0, $depth = 0) {
        $branch = array();
        foreach ($elements as $element) {
            if ($element->parentid == $parentid) {
                $children = $this->buildtree($elements, $element->id, $depth++);
                if ($children) {
                    $element->children[] = $children;
                }
                $prefix = "";
                for ($i = 0; $i <= $depth; $i++) {
                    $prefix .= "-";
                }
                $branch[] = $element;
            }
        }
        return $branch;
    }

    public function buildchildren(&$array, $node, $delimiter) {
        if (!isset($node->children)) {
            return;
        }
        foreach ($node->children as $row => $child) {
            foreach ($child as $row2 => $c) {
                $array[$c->id] = $delimiter."".$c->title;

                if (isset($c->children)) {
                    $this->buildchildren($array, $c, $delimiter.'-');
                }
            }
        }
    }

    public function buildselect(int $root) {
        global $DB;
        $categories = $DB->get_records_sql("SELECT * FROM {local_wb_faq_entry} WHERE type = '0' ORDER BY parentid, type ");
        $tree = $this->buildtree($categories, $root);
        $option = [];
        $nodes = $tree;

        foreach ($nodes as $node) {
            $option[$node->id] = $node->title;
            if (isset($node->children)) {
                $this->buildchildren($option, $node, '-');
            }
        }
        return $option;
    }

    public function buildsearch($elements, $parentid = 0, $depth = 0) {
        $branch = array();
        foreach ($elements as $element) {
            if ($element->parentid == $parentid) {
                $children = $this->buildsearch($elements, $element->id, $depth++);
                if ($children) {
                    $element->children[] = $children;
                }
                $branch[] = $element;
            }
        }
        return $branch;
    }

    public function buildsearchchildren(&$array, $node) {

        if (!isset($node->children)) {
            return;
        }
        foreach ($node->children as $row => $child) {
            foreach ($child as $row2 => $c) {
                $array[$c->id] = $c;
                if (isset($c->children)) {
                    $this->buildsearchchildren($array, $c);
                }
            }
        }
    }

    /**
     * Undocumented function
     *
     * @param integer $root
     * @return array
     */
    public function buildsearchtree(int $root) {
        global $DB, $USER;
        $faqstring = get_string('faq', 'local_wb_faq');
        $entries = $DB->get_records_sql("SELECT t1.*, coalesce(t2.title, '$faqstring') AS parenttitle FROM {local_wb_faq_entry}
        t1 left join {local_wb_faq_entry} t2 on t1.parentid = t2.id WHERE t1.enabled = 1 ORDER BY type, parentid, sortorder
        ");
        $userid = $USER->id;
        foreach ($entries as $key => $entry) {
            if (isset($entry->courseid) && $entry->courseid > 0 && !$this->has_access_to_faq_category($entry->courseid, $userid)) {
                unset($entries[$key]);
            }
        }
        $tree = $this->buildsearch($entries, $root);
        $option = [];
        $nodes = $tree;

        foreach ($nodes as $node) {
            $option[$node->id] = $node;
            if (isset($node->children)) {
                $this->buildsearchchildren($option, $node);
            }
        }
        return array_values($option);
    }

    /**
     * Returns the parent-child tree as array or json string
     * @param boolean $json
     * @param int $root - root level from faq
     * @return mixed
     * @param boolean $allowedit
     * @param integer $entryid
     * @return void
     */
    public function load_from_cache(bool $json = false, $root = null, $allowedit = false, $entryid = 0) {
        /* TODO only load relevant data */
        global $USER;
        $userid = $USER->id;
        $cache = \cache::make('local_wb_faq', 'faqcache');
        $cachekey = $allowedit ? 'faq_cache_edit' : 'faq_cache';
        $cachedrawdata = $cache->get($cachekey);

        $parentids = [];
        if (!$cachedrawdata) {
            $this->update_cache($allowedit);
            $cachedrawdata = $cache->get($cachekey);
        }

        /* Not yet perfect way to disable the nodes above content */
        if ($root) {
            $cachedrawdata[$root]->toplevel = true;
            $parentid = $root;
            do {
                if (isset($cachedrawdata[$parentid]) && isset($cachedrawdata[$parentid]->parentid)) {
                    $parentid = $cachedrawdata[$parentid]->parentid;
                    $parentids[] = $parentid;
                } else {
                    $parentid = null;
                }
            } while (!empty($parentid));
        }
        foreach ($parentids as $parentid) {
            unset($cachedrawdata[$parentid]);
        }

        // Check ACCESS.
        foreach ($cachedrawdata as $id => $node) {
            if (isset($node->courseid) && !$this->has_access_to_faq_category($node->courseid, $userid)) {
                unset($cachedrawdata[$node->id]);
            } else {

                if (!empty($entryid) && !empty($node->entries)) {
                    foreach ($node->entries as $key => $value) {
                        if ($value->id == $entryid) {
                            $node->entries[$key]->open = true;
                        }
                    }

                    $node->entries = array_values($node->entries);
                }

                if (isset($node->categories)) {

                    foreach ($node->categories as $key => $category) {
                        $set = 0;
                        if (isset($category->courseid) && !$this->has_access_to_faq_category($category->courseid, $userid)) {
                            unset($cachedrawdata[$id]->categories[$key]);
                            $set = 1;
                        }
                        if ($set) {
                            $cachedrawdata[$id]->categories = array_values($cachedrawdata[$id]->categories);
                        }
                    }
                }
            }
        }

        if ($json) {
            return json_encode($cachedrawdata, true);
        }
        return $cachedrawdata;
    }

    /**
     * Loads the tree with parent children nodes
     * @param boolean $allowedit
     * @return array
     */
    public function load_tree($allowedit = false) {
        global $DB;

        $context = context_system::instance();

        $records = $DB->get_records_sql("SELECT * FROM {local_wb_faq_entry} faq ORDER BY parentid, type ASC, sortorder DESC");
        $recordsvalues = array_values($records);
        $dataarr = [];
        foreach ($recordsvalues as $record) {

            if (has_capability('local/wb_faq:canedit', $context)) {
                if ($record->enabled != 1) {
                    $record->enabled = false;
                }
            } else {
                if ($record->enabled != 1) {
                    continue;
                }
            }

            // We need the canedit key on every record.
            // This add extra edit buttons on in the mustache template.
            if ($allowedit && has_capability('local/wb_faq:canedit', $context)) {
                $record->canedit = true;
            }

            // If its a category...
            if ($record->type == 0) {

                // If we have a category which is already in the data array, we need to keep all the information.

                if (isset($dataarr[$record->id])) {
                    foreach ($dataarr[$record->id] as $key => $value) {
                        $record->{$key} = $value;
                    }
                }
                $dataarr[$record->id] = $record;

                if (!isset($dataarr[$record->parentid])) {
                    $dataarr[$record->parentid] = new stdClass();
                }
                $dataarr[$record->parentid]->categories[] = $record;

                if ($record->parentid == 0) {
                    if (!$dataarr[$record->parentid]) {
                        $dataarr[$record->parentid] = new stdClass();
                    }
                    $dataarr[$record->parentid]->title = get_string('faq', 'local_wb_faq');
                    $dataarr[$record->parentid]->toplevel = true;
                    $dataarr[$record->parentid]->parentid = 0;
                    if ($allowedit && has_capability('local/wb_faq:canedit', $context)) {
                        $dataarr[$record->parentid]->canedit = true;
                    }
                }
            }
            $record->title = format_string($record->title);
            // If its a question.
            if ($record->type == 1) {

                $record->content = file_rewrite_pluginfile_urls(
                    $record->content,
                    'pluginfile.php',
                    $context->id,
                    'local_wb_faq',
                    'faq_entry',
                    $record->id);

                $record->content = format_text($record->content);

                if (!isset($dataarr[$record->parentid])) {
                    $dataarr[$record->parentid] = new stdClass();
                }

                // $dataarr[$record->parentid]->entries[$record->id] = $record;
                $dataarr[$record->parentid]->entries[] = $record;
            }
        }

        self::add_breadcrumb($dataarr[0], $dataarr);

        // TODO: Not yet finished.
        // if (has_capability('local/wb_faq:canedit', $context)) {
        // self::show_orphaned_entries($dataarr);
        // }

        return $dataarr;
    }

    /**
     * Check user access in course
     *
     * @param int $courseid
     * @param int|null $userid
     *
     * @return bool
     */
    private function has_access_to_faq_category($courseid, $userid = null) {

        $context = context_system::instance();

        if (has_capability('local/wb_faq:canedit', $context)) {
            return true;
        }
        if (!isset($this->courselist)) {
            $this->set_courselist();
        }
        if (isset($this->courselist[$courseid])) {
            return true;
        }

        if (empty(get_config('local_wb_faq', 'accesonlyowncourses'))) {
            return true;
        }

        return false;
    }

    /**
     * Set courselist variable
     */
    public function set_courselist() {
        $this->courselist = enrol_get_my_courses(null, null, 0, [], true);
    }

    /**
     * Get mapped strings
     *
     * @return array
     */
    public static function get_string_map() {
        $stringmap = json_decode(get_config('local_wb_faq', 'mapstrings'), true);
        return $stringmap;
    }

    /**
     * Add Breadcrumbs to flat & hierarchical tree.
     *
     * @param stdClass $node
     * @param array $flattree
     * @return void
     */
    private static function add_breadcrumb(&$node, &$flattree) {
        $map = self::get_string_map() ?? [];

        if (!$node) {
            return;
        }

        if (!isset($node->breadcrumbs)) {
            $node->breadcrumbs[] = [
                'name' => $node->title ?? get_string('faq', 'local_wb_faq'),
                'datacat' => $node->title ?? get_string('faq', 'local_wb_faq'),
                'id' => $node->id ?? 0
            ];
        }

        if (isset($node->categories)) {
            foreach ($node->categories as $category) {
                $category->breadcrumbs = $node->breadcrumbs ?? [];
                $title = $map[$category->title] ?? $category->title;
                $category->breadcrumbs[] = [
                    'name' => $title,
                    'id' => $category->id ?? 0,
                    'datacat' => $category->title,
                ];

                self::add_breadcrumb($category, $flattree);
            }
        }
        if (isset($node->id)) {
            if (!empty($node->breadcrumbs)) {
                $lastkey = array_key_last($node->breadcrumbs);
                $node->breadcrumbs[$lastkey]['active'] = true;
            }
            $flattree[$node->id]->breadcrumbs = $node->breadcrumbs;
            $flattree[$node->id]->headertitle = end($node->breadcrumbs);
        }
    }

    /**
     * This function shows orphaned questions as if the were still here.
     * But the functionality is unfinished. It's not yet possible to get them back.
     *
     * @param [type] $dataarr
     * @return void
     */
    private static function show_orphaned_entries(&$dataarr) {

        $counter = 1;
        foreach ($dataarr as $key => $category) {
            if (!isset($category->title) && isset($category->entries)) {
                // Found orphan questions.
                $category->title = get_string('orphanentries', 'local_wb_faq') . " $counter";
                $category->type = 0;
                $category->sortorder = 0;
                $category->parentid = 0;
                $category->id = $key;
                $dataarr[0]->categories[] = $category;
                $counter++;
            }
        }
    }

    /**
     * Undocumented function
     *
     * @param array|null $datatree
     * @return array
     */
    public function category_select_tree(array $datatree = null): array {
        if (!$datatree) {
            $cache = \cache::make('local_wb_faq', 'faqcache');
            $cachekey = 'faq_cache';
            $cachedrawdata = $cache->get($cachekey);
        }
        $options = [];
        // Todo: buildTree().
        return $options;

    }
    /**
     *
     * This is to create a new entity in the database
     * @param stdClass $data
     * @param boolean $allowedit
     * @return integer
     */
    public function create_faq(stdClass $data, $allowedit = false): int {
        global $DB, $USER;

        $data->timecreated = time();
        $data->timemodified = time();

        $id = $DB->insert_record('local_wb_faq_entry', $data);

        $data->id = $id;
        $data = file_postupdate_standard_editor(
            // The submitted data.
            $data,
            // The field name in the database.
            'content',
            // The options.
            self::get_textfield_options(),

            'local_wb_faq',
            'faq_entry',
            $id ?? 1 // We don't have this id yet.
        );

        $DB->update_record('local_wb_faq_entry', $data);

        $context = context_system::instance();
        $userid = $USER->id;
        // $event = \local_wb_faq\event\faq_entry_added::create(
        //     [
        //         'objectid' => $id,
        //         'context' => $context,
        //         'relateduserid' => $userid,
        //         'other' => [],
        //     ]
        // );
        // $event->trigger();

        cache_helper::purge_by_event('setbackfaqlist');

        return $id;
    }

    /**
     *
     * This is to update the entity based on the data object
     *
     * @param stdClass $data
     * @param boolean $allowedit
     * @return integer
     */
    public function update_faq(stdClass $data, $allowedit = false): int {
        global $DB, $USER;

        $data = file_postupdate_standard_editor(
            // The submitted data.
            $data,
            // The field name in the database.
            'content',
            // The options.
            self::get_textfield_options(),
            context_system::instance(),
            'local_wb_faq',
            'faq_entry',
            $data->id
        );

        $data->timemodified = time();
        $update = $DB->update_record('local_wb_faq_entry', $data);

        // Here we also need to permanently save the files.

        cache_helper::purge_by_event('setbackfaqlist');
        if ($update) {
            $context = \context_system::instance();
            $userid = $USER->id;
            $event = \local_wb_faq\event\faq_entry_updated::create(
                array(
                    'objectid' => $data->id,
                    'context' => $context,
                    'relateduserid'    => $userid));
            $event->trigger();
        }

        return $update;
    }

    /**
     * Delete faq entry.
     *
     * @param integer $id
     * @return int
     */
    public static function delete_entry(int $id) {
        global $DB, $USER;

        $result = $DB->delete_records('local_wb_faq_entry', ['id' => $id]);

        cache_helper::purge_by_event('setbackfaqlist');
        if ($result) {
            $context = context_system::instance();
            $userid = $USER->id;
            // $event = \local_wb_faq\event\faq_entry_deleted::create(
            //     [
            //         'objectid' => $id,
            //         'context' => $context,
            //         'relateduserid' => $userid,
            //         'other' => [],
            //     ]
            // );
            // $event->trigger();
        }
        return $result ? 1 : 0;
    }

    /**
     * Render entry enabled.
     * 0 is now invisible, 1 is now visibile and 2 means record couldn't be found.
     *
     * @param int $id
     * @return int
     */
    public static function toggle_entry_visibility(int $id) {
        global $DB;

        // Fetch the record.
        $record = $DB->get_record('local_wb_faq_entry', array('id' => $id));

        if (!$record) {
            return 2; // Record couldn't be found.
        }

        $record->enabled = $record->enabled == 1 ? 0 : 1;
        $record->timemodified = time();

        $DB->update_record('local_wb_faq_entry', $record);

        cache_helper::purge_by_event('setbackfaqlist');

        return $record->enabled;
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
     * Return category by name.
     *
     * @param string $name
     * @return null|int
     */
    public static function return_category_id_by_name(string $name) {

        global $DB;

        if (empty($name)) {
            return 0;
        }

        if (!$records = $DB->get_records('local_wb_faq_entry', ['type' => 0, 'title' => $name])) {
            return null;
        } else {
            if (count($records) === 1) {

                $record = reset($records);

                return (int)$record->id;
            } else {
                return null;
            }
        }
    }

    /**
     * Returns the parentid from the entryid
     *
     * @param int $entryid
     * @return null|int
     */
    public static function get_parentid_from_entryid(int $entryid) {
        global $DB;
        if (!$DB->record_exists('local_wb_faq_entry', array('id' => $entryid))) {
            return null;
        }
        return $DB->get_field('local_wb_faq_entry', 'parentid', array('id' => $entryid));
    }

    /**
     * Returns the relatedcourseid from the entryid
     * TODO: replace parentid with releatedcourstableid
     * @param int $entryid
     * @return null|int
     */
    public static function get_related_courseid_from_entryid(int $entryid) {
        global $DB;

        $record = $DB->get_record('local_wb_faq_entry', array('id' => $entryid));

        // Check if the entry is a question and has no courseid.
        if (($record->type == 1) && empty($record->courseid)) {

            // If not, we look for a courseid in parent.
            if (!empty($record->parentid)) {
                $parentrecord = $DB->get_record('local_wb_faq_entry', ['id' => $record->parentid]);

                if (!empty($parentrecord->courseid)) {
                    return (int)$parentrecord->courseid;
                } else {
                    return 0;
                }
            }

        } else {
            return (int)$record->courseid ?? 0;
        }
    }

    /**
     * Sets order of input array id order
     *
     * @param array $ids
     * @return string error
     */
    public static function set_order(array $ids): string {
        global $DB;
        $len = count($ids);

        for ($i = 0; $i < $len; $i++) {
            $data = new stdClass();
            $data->id = $ids[$i];
            // Sortorder from top to bottom (-1 to reach zero).
            $data->sortorder = $len - $i - 1;
            $DB->update_record('local_wb_faq_entry', $data, true);
        }
        cache_helper::purge_by_event('setbackfaqlist');

        return 'none';
    }

    /**
     * Add some form elements.
     *
     * @param [type] $mform
     * @return void
     */
    public static function add_form_elements(&$mform, array $ajaxformdata) {

        $group = $ajaxformdata["supplement"] ?? '';

        list($groupselection, $modulesselction) = self::return_modules_and_groups($group);

        if (count($groupselection) > 1) {
            $mform->addElement(
                'select',
                'supplement',
                get_string('groups', 'local_wb_faq'),
                $groupselection,
                ['data-on-change-action' => "reloadForm"],
            );

            if (!empty($group)) {
                $mform->addElement('select', 'module', get_string('modules', 'local_wb_faq'), $modulesselction);

                if (!in_array($ajaxformdata['module'], array_keys($modulesselction))) {
                    $mform->setDefault('module', 0);
                }
            }
        }

        // Button to attach JavaScript to to reload the form.
        $mform->registerNoSubmitButton('groupsubmit');
        $mform->addElement('submit', 'groupsubmit', 'groupsubmit',
            ['class' => 'd-none', 'data-action' => 'groupSubmit']);
    }

    /**
     * Function to return the groups and modules array, depending on the selected group.
     *
     * @param string $group
     * @return [array]
     */
    public static function return_modules_and_groups($group) {
        // Get an array from the settings.
        $groupsnmodules = explode(PHP_EOL, get_config('local_wb_faq', 'groupsnmodules'));
        $groups = [0 => get_string('pleasechoose', 'local_wb_faq')];
        $modules = [0 => get_string('pleasechoose', 'local_wb_faq')];

        foreach ($groupsnmodules as $line) {
            if (empty($line)) {
                continue;
            }
            list($shortgroup, $namegroup, $shortmodule, $namemodule) = explode(',', $line);
            $shortgroup = trim($shortgroup);
            $groups[$shortgroup] = $namegroup;

            // We only add the modules for the selected group.
            if ($group == $shortgroup) {
                $shortmodule = trim($shortmodule);
                $modules[$shortmodule] = $namemodule;
            }
        }

        return [$groups, $modules];
    }

    /**
     * As we need it twice, we create a function.
     * @return array
     */
    public static function get_textfield_options() {

        $context = context_system::instance();

        return [
            'trusttext' => true,
            'subdirs' => true,
            'context' => $context,
            'maxfiles' => EDITOR_UNLIMITED_FILES,
            'noclean' => true,
        ];
    }
}
