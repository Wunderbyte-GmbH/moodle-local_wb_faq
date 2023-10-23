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

$string['editcategories'] = "Kategorien editieren";
$string['editquestions'] = "Fragen editieren";
$string['addquestion'] = "Frage hinzufügen";
$string['addcategory'] = "Kategorie hinzufügen";

$string['delete'] = "Delete";
$string['wb_faq:copymodule'] = "wb_faq: copy module";
$string['categories'] = "Standardcategories";
$string['categories:description'] = "Set the Standardcategory from the list of the customfieldcategories visibile on ever edit page";
$string['er_wb_faqname'] = "Ausgewählte Entität";

$string['input:title'] = 'Titel';
$string['input:question'] = 'Frage';
$string['input:content'] = 'Inhalt';
$string['input:sortorder'] = 'Sortierung';
$string['input:type'] = 'Kategorie';
$string['input:parentid'] = 'Übergeordnete Kategorie';
$string['searchcategory'] = '... durchsuche Kategorien';
$string['root'] = 'Oberste Ebene';

$string['wb_faq:canedit'] = 'Verwalte FAQ';

// Shortcodes.
$string['displayfaqs'] = "Zeige FAQs";
$string['categorynotfound'] = 'Kategorie "{$a}" nicht gefunden';

$string['faq'] = "FAQ";
$string['noresult'] = "Kein Ergebnis gefunden.";
$string['stringtooshort'] = "Eingabe zu kurz...";
$string['searchfaq'] = "... durchsuche FAQs";

// Edit.
$string['confirmdeleteentry'] = "Bestätige das Löschen";
$string['confirmdeleteentrytitle'] = "Bestätige das Löschen";
$string['confirmdeleteentrybody'] = "Möchten Sie wirklich diesen Eintrag löschen?";
$string['orphanentries'] = "Verweiste Einträge";

$string['confirmtogglevisibilitytitle'] = "Wollen Sie wirklich die Sichtbarkeit ändern?";
$string['confirmtogglevisibilitybody'] = "Wenn Sie die Sichtbarkeit ändern sind die Einträge
    nur noch für Userinnen mit entsprechenden Bearbeitungsrechten sichtbar.";
$string['confirmtogglevisibility'] = "Ändere die Sichtbarkeit";

// Notification.
$string['saveerror'] = "Fehler beim Speichern";
$string['savesuccess'] = "Erfolgreich gespeichert";

// Search API.
$string['search:faqentry'] = 'FAQ - entry';

// Edit CategoriesForm.
$string['choosecourse'] = "Einen Kurs auswählen. Das bestimmt, wer diese Kategorie und alle FAQs darin sehen kann.";
$string['nocourseselected'] = "Kein Kurs ausgewählt";
$string['invisible'] = "Sichtbar";
$string['groupsnmodules'] = 'Gruppen und Module';
$string['minmessagelength'] = 'Minimale Länge einer Support Nachricht';
$string['thankyouforsupportmessage'] = "Geben Sie hier den Text ein, um Ihren NutzerInnen für Supportanfragen zu danken.";

$string['priority'] = "Priorität";
$string['groups'] = "Gruppen";
$string['modules'] = "Module";
$string['title'] = "Titel";
$string['message'] = "Nachricht";
$string['normal'] = "normal";
$string['low'] = "niedrig";
$string['medium'] = "mittel";
$string['high'] = "hoch";
$string['pleasechoose'] = "Bitte wählen Sie";
$string['thankyoutext'] = "Danke für ihre Nachricht! Unser Support wird sich mit Ihnen in Verbindung setzen!";

// Supportmessage.
$string['searchfaqs'] = "FAQs";
$string['writemessage'] = "Nachricht";
$string['thankyou'] = "Danke";
$string['attachment'] = 'Dateianhang';

// Error messages.
$string['entertitle'] = "Bitte geben Sie einen Titel an";
$string['entermessage'] = "Ihre Nachricht ist zu kurz";
$string['entergroup'] = "Bite wählen Sie eine Gruppe";
$string['entermodule'] = "Bite wählen Sie ein Modul";

// Rest interface.
$string['resturl'] = "Url für externes Support system";
$string['resturl_desc'] = "Die Supportanfrage kann an ein externes System weitergeleitet werden.";
$string['usesupport'] = "Zeige Support Link in der Navigationsleiste";
$string['usesupport_desc'] = "Der Support Link gibt einfachen Zugang zu den FAQs";
$string['imagetoken'] = "Token um Bilder herunterzuladen.";
$string['employer'] = 'Arbeitgeber';

// Capabilities.
$string['wb_faq:accessimages'] = "Zugriff auf Bilder";
$string['wb_faq:cansendsupportmessage'] = "Kann Supportnachricht verschicken";

// Settings.
$string['accesonlyowncourses'] = 'Erlaube Zugang nur zu eigenen Kursen';
$string['accesonlyowncourses_desc'] = 'NutzerInnen können nur FAQs sehen, die Kursen, für die die NutzerInnen Zugang haben, freigegeben wurden.';
