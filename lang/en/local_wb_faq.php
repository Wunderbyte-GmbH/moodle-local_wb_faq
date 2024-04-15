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
$string['contactsupport'] = "Contact Support";
$string['createticket'] = 'Create Ticket';
$string['jwtsecret'] = "JWT Secret";
$string['jwtapp'] = "JWT App";
$string['supportmessagebaseurl'] = "Support message base i";

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

// Shortcodes.

$string['displayfaqs'] = "Display FAQs";
$string['categorynotfound'] = 'Category "{$a}" not found';

$string['faq'] = "FAQ";
$string['noresult'] = "Couln't find anything...";
$string['stringtooshort'] = "Input too short...";
$string['searchfaq'] = "... search faq";

// Edit.
$string['confirmdeleteentry'] = "Confirm delete entry";
$string['confirmdeleteentrytitle'] = "Confirm delete entry";
$string['confirmdeleteentrybody'] = "Do you really want to delte this entry?";
$string['orphanentries'] = "Orphan entries";

$string['confirmtogglevisibilitytitle'] = "Do you really want to change the visibility?";
$string['confirmtogglevisibilitybody'] = "When you change the visibility, only users with corresponding
    edit-capabilities can still see these entries.";
$string['confirmtogglevisibility'] = "Change visibility";

// Notification.
$string['saveerror'] = "Error while saving";
$string['savesuccess'] = "Successfully saved";

// Search api.
$string['search:faqentry'] = 'FAQ - Eintrag';

// Edit CategoriesForm.
$string['choosecourse'] = "Choose a course. This determines who can see this category and all the faqs inside";
$string['nocourseselected'] = "No course selected";
$string['invisible'] = "Visible";
$string['groupsnmodules'] = 'Groups and modules';
$string['minmessagelength'] = 'Min support message length';
$string['thankyouforsupportmessage'] = "You can enter the text to thank your users for sending support messages.";

$string['priority'] = "Priority";
$string['groups'] = "Groups";
$string['modules'] = "Modules";
$string['title'] = "Title";
$string['message'] = "Message";
$string['normal'] = "normal";
$string['low'] = "low";
$string['medium'] = "medium";
$string['high'] = "high";
$string['pleasechoose'] = "Please choose";
$string['thankyoutext'] = "Thank you for your message! Our support will get in touch with you!";

// Supportmessage.
$string['searchfaqs'] = "FAQs";
$string['writemessage'] = "Message";
$string['thankyou'] = "Thank You";
$string['attachment'] = 'Attachment';

// Error messages.
$string['entertitle'] = "Please enter a title";
$string['entermessage'] = "Your message is too short";
$string['entergroup'] = "Please choose a group";
$string['entermodule'] = "Please choose a module";

// Rest interface.
$string['resturl'] = "Url for rest interface";
$string['resturl_desc'] = "The request for support can be sent to an external system";
$string['usesupport'] = "Show the support icon in the navbar";
$string['usesupport_desc'] = "The Support Icon in the navbar gives easy access to the faqs";
$string['imagetoken'] = "Token to download images";
$string['employer'] = 'Employer';

// Capabilities.
$string['wb_faq:accessimages'] = "Access images";
$string['wb_faq:cansendsupportmessage'] = "Can send support message";

// Settings.
$string['accesonlyowncourses'] = 'Restrict acces to own courses';
$string['accesonlyowncourses_desc'] = 'Users can access course specific faqs only if they have access to these courses';

$string['entrydeleted'] = "Entry was deleted";
$string['nocourseid'] = "Please select a course for this entry.";

$string['supportactioncreate'] = 'Create';
$string['createsupportticket'] = 'Create support ticket';

// Support.
$string['support'] = 'Support';
$string['supportanfrage'] = 'Support Anfrage';
$string['supportweiterbildung'] = 'Aus- und Weiterbildung <i class="fa fa-angle-double-right" aria-hidden="true"></i>';
$string['supportvertrieb'] = 'Anfrage an den Vertrieb <i class="fa fa-angle-double-right" aria-hidden="true"></i>';
$string['supportstoerung'] = 'Störung melden <i class="fa fa-angle-double-right" aria-hidden="true"></i>';
$string['supportmytickets'] = 'Meine Tickets <i class="fa fa-angle-double-right" aria-hidden="true"></i>';
$string['team'] = 'Comm-Unity Team <i class="fa fa-angle-double-right" aria-hidden="true"></i>';

// Debug.
$string['debug'] = 'Debug mode';
$string['debug_desc'] = 'Debug mode will eg. echo the jwt token instead of redirecting directly';
