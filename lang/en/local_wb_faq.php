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

$string['accesonlyowncourses'] = 'Restrict acces to own courses';
$string['accesonlyowncourses_desc'] = 'Users can access course specific faqs only if they have access to these courses';
$string['addcategory'] = "Add category";
$string['addentity'] = 'Add Entity';
$string['addquestion'] = "Add question";
$string['address'] = 'Address';
$string['address_city'] = 'City';
$string['address_country'] = 'Country';
$string['address_postcode'] = 'Postcode';
$string['address_streetname'] = 'Street name';
$string['address_streetnumber'] = 'Street number';
$string['attachment'] = 'Attachment';
$string['backtolist'] = 'Back to Entity Manager';
$string['cachedef_faqcache'] = "Frequently Asked Questions.";
$string['categories'] = "Standardcategories";
$string['categories:description'] = "Set the Standardcategory from the list of the customfieldcategories visibile on ever edit page";
$string['categorynotfound'] = 'Category "{$a}" not found';
$string['choosecourse'] = "Choose a course. This determines who can see this category and all the faqs inside";
$string['confirmdeleteentry'] = "Confirm delete entry";
$string['confirmdeleteentrybody'] = "Do you really want to delte this entry?";
$string['confirmdeleteentrytitle'] = "Confirm delete entry";
$string['confirmtogglevisibility'] = "Change visibility";
$string['confirmtogglevisibilitybody'] = "When you change the visibility, only users with corresponding
    edit-capabilities can still see these entries.";
$string['confirmtogglevisibilitytitle'] = "Do you really want to change the visibility?";
$string['contacts'] = 'Contacts';
$string['contacts_givenname'] = 'Given name';
$string['contacts_mail'] = 'E-Mail';
$string['contacts_surname'] = 'Surname';
$string['contactsupport'] = "Contact Support";
$string['createsupportticket'] = 'Create support ticket';
$string['createticket'] = 'Create Ticket';
$string['debug'] = 'Debug mode';
$string['debug_desc'] = 'Debug mode will eg. echo the jwt token instead of redirecting directly';
$string['delete'] = "Delete";
$string['displayfaqs'] = "Display FAQs";
$string['draweropen'] = "Block open after login";
$string['draweropen_desc'] = "With this setting, the block on the right side can be forced open after every new login from a user.";
$string['edit'] = "Edit";
$string['edit_details'] = 'Edit details';
$string['edit_entity'] = 'Edit Entity';
$string['edit_image'] = 'Edit image';
$string['editcategories'] = "Edit categories";
$string['editquestions'] = "Edit questions";
$string['employer'] = 'Employer';
$string['entergroup'] = "Please choose a group";
$string['entermessage'] = "Your message is too short";
$string['entermodule'] = "Please choose a module";
$string['entertitle'] = "Please enter a title";
$string['entity_category'] = 'Entity category';
$string['entity_description'] = 'Entity description';
$string['entity_name'] = 'Entity name';
$string['entity_order'] = 'Sort order';
$string['entity_parent'] = 'Entity parent';
$string['entity_title'] = 'Entity';
$string['entitysetup_heading'] = 'Edit or Create Entity';
$string['entrydeleted'] = "Entry was deleted";
$string['er_wb_faqname'] = "selected entitiy";
$string['faq'] = "FAQ";
$string['groups'] = "Groups";
$string['groupsnmodules'] = 'Groups and modules';
$string['high'] = "high";
$string['imagetoken'] = "Token to download images";
$string['input:content'] = 'Content';
$string['input:parentid'] = 'Parent';
$string['input:question'] = 'Question';
$string['input:sortorder'] = 'Sortorder';
$string['input:title'] = 'Title';
$string['input:type'] = 'Category';
$string['invisible'] = "Visible";
$string['jwtapp'] = "JWT App";
$string['jwtsecret'] = "JWT Secret";
$string['low'] = "low";
$string['mapstrings'] = 'Change breadcrumb strings in a JSON Map e.g. {"Cat1": "User"}';
$string['medium'] = "medium";
$string['message'] = "Message";
$string['minmessagelength'] = 'Min support message length';
$string['modules'] = "Modules";
$string['new_entity'] = 'New Entity';
$string['nocourseid'] = "Please select a course for this entry.";
$string['nocourseselected'] = "No course selected";
$string['none'] = 'None';
$string['noresult'] = "Couln't find anything...";
$string['normal'] = "normal";
$string['orphanentries'] = "Orphan entries";
$string['pleasechoose'] = "Please choose";
$string['pluginname'] = 'FAQ';
$string['priority'] = "Priority";
$string['privacy:metadata'] = 'The FAQ plugin does not store any personal data.';
$string['resturl'] = "Url for rest interface";
$string['resturl_desc'] = "The request for support can be sent to an external system";
$string['root'] = 'Top Level';
$string['saveerror'] = "Error while saving";
$string['savesuccess'] = "Successfully saved";
$string['search:faqentry'] = 'FAQ - Eintrag';
$string['searchcategory'] = '... search category';
$string['searchfaq'] = "... search faq";
$string['searchfaqs'] = "FAQs";
$string['stringtooshort'] = "Input too short...";
$string['support'] = 'Support';
$string['supportactioncreate'] = 'Create';
$string['supportanfrage'] = 'Support Anfrage';
$string['supportmessagebaseurl'] = "Support message base i";
$string['supportmytickets'] = 'Meine Tickets <i class="fa fa-angle-double-right" aria-hidden="true"></i>';
$string['supportonlineticket'] = 'Online-Ticket';
$string['supportstoerung'] = 'Störung melden <i class="fa fa-angle-double-right" aria-hidden="true"></i>';
$string['supportvertrieb'] = 'Anfrage an den Vertrieb <i class="fa fa-angle-double-right" aria-hidden="true"></i>';
$string['supportweiterbildung'] = 'Aus- und Weiterbildung <i class="fa fa-angle-double-right" aria-hidden="true"></i>';
$string['team'] = 'Comm-Unity Team <i class="fa fa-angle-double-right" aria-hidden="true"></i>';
$string['thankyou'] = "Thank You";
$string['thankyouforsupportmessage'] = "You can enter the text to thank your users for sending support messages.";
$string['thankyoutext'] = "Thank you for your message! Our support will get in touch with you!";
$string['title'] = "Title";
$string['usesupport'] = "Show the support icon in the navbar";
$string['usesupport_desc'] = "The Support Icon in the navbar gives easy access to the faqs";
$string['view'] = "View";
$string['wb_faq:accessimages'] = "Access images";
$string['wb_faq:canedit'] = 'Manage FAQ';
$string['wb_faq:cansendsupportmessage'] = "Can send support message";
$string['wb_faq:copymodule'] = "wb_faq: copy module";
$string['writemessage'] = "Message";
