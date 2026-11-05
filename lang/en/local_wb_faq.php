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
 * Plugin strings are defined here.
 *
 * @package     local_wb_faq
 * @category    string
 * @copyright   2022 Wunderbyte GmbH <info@wunderbyte.at>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['accesonlyowncourses'] = 'Restrict access to own courses';
$string['accesonlyowncourses_desc'] = 'Categories linked to a course are only shown to users who have access to this course. Categories without a course are always shown.';
$string['addcategory'] = 'Add category';
$string['addquestion'] = 'Add question';
$string['allowanonymous'] = 'Allow access without login';
$string['allowanonymous_desc'] = 'Users who are not logged in can read all categories which are not linked to a course. This has no effect if the site forces users to log in.';
$string['breadcrumbs'] = 'FAQ navigation';
$string['categorynotfound'] = 'Category "{$a}" not found';
$string['choosecourse'] = 'Course. Only users with access to this course can see this category and everything inside of it.';
$string['confirmdeleteentry'] = 'Delete';
$string['confirmdeleteentrybody'] = 'Do you really want to delete this entry? If it is a category, everything inside of it gets deleted too.';
$string['confirmdeleteentrytitle'] = 'Confirm deletion';
$string['confirmtogglevisibility'] = 'Change visibility';
$string['confirmtogglevisibilitybody'] = 'If an entry is hidden, only users who can edit the FAQ can still see it.';
$string['confirmtogglevisibilitytitle'] = 'Do you really want to change the visibility?';
$string['displayfaqs'] = 'Display FAQs';
$string['draweropen'] = 'Open block drawer after login';
$string['draweropen_desc'] = 'With this setting, the block drawer on the right side is opened after every new login of a user.';
$string['editcategory'] = 'Edit category';
$string['editquestion'] = 'Edit question';
$string['entrydeleted'] = 'Entry was deleted';
$string['entrynotfound'] = 'This entry does not exist or you cannot access it.';
$string['entryvisible'] = 'Visible';
$string['faq'] = 'FAQ';
$string['faqentryadded'] = 'FAQ entry added';
$string['faqentrydeleted'] = 'FAQ entry deleted';
$string['faqentryupdated'] = 'FAQ entry updated';
$string['hiddenentry'] = 'Hidden';
$string['input:content'] = 'Content';
$string['input:parentid'] = 'Parent category';
$string['input:question'] = 'Question';
$string['input:title'] = 'Title';
$string['invalidorder'] = 'These entries cannot be sorted together.';
$string['invalidparent'] = 'A category cannot be moved into itself or into one of its subcategories.';
$string['loginrequired'] = 'Please log in to read the FAQ.';
$string['mapstrings'] = 'Breadcrumb labels';
$string['mapstrings_desc'] = 'Replaces the names of categories in the breadcrumbs, as a JSON map, e.g. {"Cat1": "User"}';
$string['moveentry'] = 'Move entry';
$string['nocourseselected'] = 'No course';
$string['noentries'] = 'There are no entries yet.';
$string['noresult'] = 'No results found.';
$string['pagination'] = 'Pages';
$string['perpage'] = 'Entries per page';
$string['perpage_desc'] = 'Number of questions or search results shown on one page.';
$string['pluginname'] = 'FAQ';
$string['privacy:metadata'] = 'The FAQ plugin does not store any personal data.';
$string['root'] = 'Top level';
$string['savesuccess'] = 'Successfully saved';
$string['search:faqentry'] = 'FAQ - entry';
$string['searchfaq'] = 'Search FAQ';
$string['searchresults'] = 'Search results: {$a}';
$string['stringtooshort'] = 'Please enter at least {$a} characters.';
$string['support'] = 'Help';
$string['supportlinks'] = 'Links in the help menu';
$string['supportlinks_desc'] = 'Further entries of the help menu, one per line in the format Label|URL.';
$string['togglevisibility'] = 'Change visibility';
$string['usesupport'] = 'Show the help menu in the navigation bar';
$string['usesupport_desc'] = 'The help menu gives logged in users access to the FAQ from every page.';
$string['wb_faq:canedit'] = 'Manage FAQ';
