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
 * Database access for FAQ categories and questions.
 *
 * @package    local_wb_faq
 * @copyright  2026 Wunderbyte GmbH <info@wunderbyte.at>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace local_wb_faq\local;

use stdClass;

/**
 * All SQL of the plugin lives here. The class knows nothing about users or output.
 *
 * Visibility is passed in as a filter, see {@see access::get_entry_filter()}. A filter has the keys
 * "includedisabled" (bool) and "courseids" (int[] or null, where null means no course restriction).
 *
 * @package    local_wb_faq
 * @copyright  2026 Wunderbyte GmbH <info@wunderbyte.at>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class entry_repository {
    /** @var string The table holding categories and questions. */
    public const TABLE = 'local_wb_faq_entry';

    /** @var string Value of the type field for a category. */
    public const TYPE_CATEGORY = '0';

    /** @var string Value of the type field for a question. */
    public const TYPE_QUESTION = '1';

    /** @var string Display order of siblings. */
    private const ORDER = 'sortorder DESC, id ASC';

    /**
     * Returns a single record.
     *
     * @param int $id
     * @return stdClass|null
     */
    public function get(int $id): ?stdClass {
        global $DB;

        return $DB->get_record(self::TABLE, ['id' => $id]) ?: null;
    }

    /**
     * Returns all categories in display order, indexed by id.
     *
     * @return stdClass[]
     */
    public function get_all_categories(): array {
        global $DB;

        return $DB->get_records(self::TABLE, ['type' => self::TYPE_CATEGORY], self::ORDER);
    }

    /**
     * Returns the ids of all children of one type in display order, regardless of their visibility.
     *
     * @param int $parentid
     * @param string $type
     * @return int[]
     */
    public function get_sibling_ids(int $parentid, string $type): array {
        global $DB;

        $ids = $DB->get_fieldset_select(
            self::TABLE,
            'id',
            'parentid = :parentid AND type = :type ORDER BY ' . self::ORDER,
            ['parentid' => $parentid, 'type' => $type]
        );

        return array_map('intval', $ids);
    }

    /**
     * Counts the visible questions of a category.
     *
     * @param int $parentid
     * @param array $filter
     * @return int
     */
    public function count_questions(int $parentid, array $filter): int {
        global $DB;

        [$where, $params] = $this->questions_where($parentid, $filter);

        return $DB->count_records_select(self::TABLE, $where, $params);
    }

    /**
     * Returns one page of the visible questions of a category.
     *
     * @param int $parentid
     * @param array $filter
     * @param int $offset
     * @param int $limit
     * @return stdClass[]
     */
    public function get_questions(int $parentid, array $filter, int $offset, int $limit): array {
        global $DB;

        [$where, $params] = $this->questions_where($parentid, $filter);

        return array_values($DB->get_records_select(self::TABLE, $where, $params, self::ORDER, '*', $offset, $limit));
    }

    /**
     * Returns the zero based position of a question among the visible questions of its category.
     *
     * @param stdClass $question
     * @param array $filter
     * @return int
     */
    public function get_question_position(stdClass $question, array $filter): int {
        global $DB;

        [$where, $params] = $this->questions_where((int)$question->parentid, $filter);
        $ids = $DB->get_fieldset_select(self::TABLE, 'id', $where . ' ORDER BY ' . self::ORDER, $params);

        return (int)array_search($question->id, $ids);
    }

    /**
     * Counts the search results.
     *
     * @param int[] $categoryids
     * @param string $query
     * @param array $filter
     * @return int
     */
    public function count_search(array $categoryids, string $query, array $filter): int {
        global $DB;

        [$where, $params] = $this->search_where($categoryids, $query, $filter);

        return $DB->count_records_select(self::TABLE, $where, $params);
    }

    /**
     * Returns one page of search results, categories first.
     *
     * @param int[] $categoryids Only categories and questions below these categories are searched.
     * @param string $query
     * @param array $filter
     * @param int $offset
     * @param int $limit
     * @return stdClass[]
     */
    public function search(array $categoryids, string $query, array $filter, int $offset, int $limit): array {
        global $DB;

        [$where, $params] = $this->search_where($categoryids, $query, $filter);

        return array_values(
            $DB->get_records_select(self::TABLE, $where, $params, 'type ASC, ' . self::ORDER, '*', $offset, $limit)
        );
    }

    /**
     * Returns the category with this exact title, if there is exactly one.
     *
     * @param string $title
     * @return int|null
     */
    public function find_category_id_by_title(string $title): ?int {
        global $DB;

        $records = $DB->get_records(self::TABLE, ['type' => self::TYPE_CATEGORY, 'title' => $title], '', 'id');

        return count($records) === 1 ? (int)reset($records)->id : null;
    }

    /**
     * Stores a new record.
     *
     * @param stdClass $record
     * @return int
     */
    public function insert(stdClass $record): int {
        global $DB;

        return (int)$DB->insert_record(self::TABLE, $record);
    }

    /**
     * Updates a record.
     *
     * @param stdClass $record
     * @return void
     */
    public function update(stdClass $record): void {
        global $DB;

        $DB->update_record(self::TABLE, $record);
    }

    /**
     * Deletes records.
     *
     * @param int[] $ids
     * @return void
     */
    public function delete(array $ids): void {
        global $DB;

        $DB->delete_records_list(self::TABLE, 'id', $ids);
    }

    /**
     * Returns the ids of the questions directly inside the given categories.
     *
     * @param int[] $categoryids
     * @return int[]
     */
    public function get_question_ids_in_categories(array $categoryids): array {
        global $DB;

        [$insql, $params] = $DB->get_in_or_equal($categoryids, SQL_PARAMS_NAMED);
        $params['type'] = self::TYPE_QUESTION;
        $ids = $DB->get_fieldset_select(self::TABLE, 'id', "parentid $insql AND type = :type", $params);

        return array_map('intval', $ids);
    }

    /**
     * Builds the condition for the visible questions of a category.
     *
     * @param int $parentid
     * @param array $filter
     * @return array SQL and parameters.
     */
    private function questions_where(int $parentid, array $filter): array {
        [$visibility, $params] = $this->visibility_where($filter);
        $params['parentid'] = $parentid;
        $params['type'] = self::TYPE_QUESTION;

        return ["parentid = :parentid AND type = :type AND $visibility", $params];
    }

    /**
     * Builds the condition of a search.
     *
     * @param int[] $categoryids
     * @param string $query
     * @param array $filter
     * @return array SQL and parameters.
     */
    private function search_where(array $categoryids, string $query, array $filter): array {
        global $DB;

        [$visibility, $params] = $this->visibility_where($filter);

        // A question matches inside any visible category, a category only if it is itself visible.
        [$parentsql, $parentparams] = $DB->get_in_or_equal($categoryids, SQL_PARAMS_NAMED, 'parent');
        [$idsql, $idparams] = $DB->get_in_or_equal($categoryids, SQL_PARAMS_NAMED, 'cat');
        $params += $parentparams + $idparams;
        $params['question'] = self::TYPE_QUESTION;
        $params['category'] = self::TYPE_CATEGORY;

        $matches = [];
        foreach ($this->get_query_variants($query) as $index => $variant) {
            $pattern = '%' . $DB->sql_like_escape($variant) . '%';
            $params["title$index"] = $pattern;
            $params["content$index"] = $pattern;
            $matches[] = $DB->sql_like('title', ":title$index", false);
            $matches[] = $DB->sql_like('content', ":content$index", false);
        }
        $match = implode(' OR ', $matches);

        $scope = "(type = :question AND parentid $parentsql) OR (type = :category AND id $idsql)";

        return ["$visibility AND ($scope) AND ($match)", $params];
    }

    /**
     * Returns the spellings to search for, so that "Müller" also finds "Mueller" and the other way around.
     *
     * @param string $query
     * @return string[]
     */
    private function get_query_variants(string $query): array {
        $umlauts = ['ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'ß' => 'ss', 'Ä' => 'Ae', 'Ö' => 'Oe', 'Ü' => 'Ue'];

        return array_values(array_unique([
            $query,
            strtr($query, $umlauts),
            strtr($query, array_flip($umlauts)),
        ]));
    }

    /**
     * Translates a visibility filter into SQL.
     *
     * @param array $filter
     * @return array SQL and parameters.
     */
    private function visibility_where(array $filter): array {
        global $DB;

        $where = ['1 = 1'];
        $params = [];

        if (empty($filter['includedisabled'])) {
            $where[] = 'enabled = 1';
        }

        if ($filter['courseids'] !== null) {
            // Records without a course are never restricted.
            $courseids = array_merge([0, SITEID], $filter['courseids']);
            [$insql, $courseparams] = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED, 'course');
            $where[] = "courseid $insql";
            $params += $courseparams;
        }

        return [implode(' AND ', $where), $params];
    }
}
