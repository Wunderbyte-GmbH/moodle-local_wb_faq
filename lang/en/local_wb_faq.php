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

$string['pluginname'] = 'FAQ';
$string['none'] = 'None';
$string['edit_details'] = 'Edit details';
$string['entity_name'] = 'Entity name';
$string['edit_image'] = 'Edit image';
$string['entity_parent'] = 'Entity parent';
$string['entity_order'] = 'Sort order';
$string['entity_category'] = 'Entity category';
$string['entity_description'] = 'Entity description';
$string['address'] = 'Address';
$string['address_city'] = 'City';
$string['address_country'] = 'Country';
$string['address_postcode'] = 'Postcode';
$string['address_streetname'] = 'Street name';
$string['address_streetnumber'] = 'Street number';
$string['contacts'] = 'Contacts';
$string['contacts_givenname'] = 'Given name';
$string['contacts_surname'] = 'Surname';
$string['contacts_mail'] = 'E-Mail';
$string['addentity'] = 'Add Entity';
$string['entitysetup_heading'] = 'Edit or Create Entity';
$string['entity_title'] = 'Entity';
$string['backtolist'] = 'Back to Entity Manager';
$string['new_entity'] = 'New Entity';
$string['edit_entity'] = 'Edit Entity';
$string['view'] = "View";
$string['edit'] = "Edit";

$string['editcategories'] = "Edit categories";
$string['editquestions'] = "Edit questions";
$string['addquestion'] = "Add question";
$string['addcategory'] = "Add category";

$string['delete'] = "Delete";
$string['wb_faq:copymodule'] = "wb_faq: copy module";
$string['categories'] = "Standardcategories";
$string['categories:description'] = "Set the Standardcategory from the list of the customfieldcategories visibile on ever edit page";
$string['er_wb_faqname'] = "selected entitiy";

$string['input:title'] = 'Title';
$string['input:question'] = 'Question';
$string['input:content'] = 'Content';
$string['input:sortorder'] = 'Sortorder';
$string['input:type'] = 'Category';
$string['input:parentid'] = 'Parent';
$string['searchcategory'] = '... search category';
$string['root'] = 'Top Level';

$string['wb_faq:canedit'] = 'Manage FAQ';

// Shortcodes

$string['displayfaqs'] = "Display FAQs";
$string['categorynotfound'] = 'Category "{$a}" not found';

$string['faq'] = "FAQ";
$string['noresult'] = "Couln't find anything...";
$string['stringtooshort'] = "Input too short...";
$string['searchfaq'] = "... search faq";

// edit.js
$string['confirmdeleteentry'] = "Confirm delete entry";
$string['confirmdeleteentrytitle'] = "Confirm delete entry";
$string['confirmdeleteentrybody'] = "Do you really want to delte this entry?";
$string['orphanentries'] = "Orphan entries";

$string['confirmtogglevisibilitytitle'] = "Do you really want to change the visibility?";
$string['confirmtogglevisibilitybody'] = "When you change the visibility, only users with corresponding
    edit-capabilities can still see these entries.";
$string['confirmtogglevisibility'] = "Change visibility";

// notification.js
$string['saveerror'] = "Error while saving";
$string['savesuccess'] = "Successfully saved";

// Search api.
$string['search:faqentry'] = 'FAQ - Eintrag';

// editCategoriesForm
$string['choosecourse'] = "Choose a course. This determines who can see this category and all the faqs inside";
$string['nocourseselected'] = "No course selected";
$string['invisible'] = "Visible";
