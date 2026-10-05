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

$string['accesonlyowncourses'] = 'Erlaube Zugang nur zu eigenen Kursen';
$string['accesonlyowncourses_desc'] = 'NutzerInnen können nur FAQs sehen, die Kursen, für die die NutzerInnen Zugang haben, freigegeben wurden.';
$string['addcategory'] = "Kategorie hinzufügen";
$string['addentity'] = 'Add Entity';
$string['addquestion'] = "Frage hinzufügen";
$string['address'] = 'Address';
$string['address_city'] = 'City';
$string['address_country'] = 'Country';
$string['address_postcode'] = 'Postcode';
$string['address_streetname'] = 'Street name';
$string['address_streetnumber'] = 'Street number';
$string['attachment'] = 'Dateianhang';
$string['backtolist'] = 'Back to Entity Manager';
$string['cachedef_faqcache'] = "Frequently Asked Questions.";
$string['categories'] = "Standardcategories";
$string['categories:description'] = "Set the Standardcategory from the list of the customfieldcategories visibile on ever edit page";
$string['categorynotfound'] = 'Kategorie "{$a}" nicht gefunden';
$string['choosecourse'] = "Einen Kurs auswählen. Das bestimmt, wer diese Kategorie und alle FAQs darin sehen kann.";
$string['confirmdeleteentry'] = "Bestätige das Löschen";
$string['confirmdeleteentrybody'] = "Möchten Sie wirklich diesen Eintrag löschen?";
$string['confirmdeleteentrytitle'] = "Bestätige das Löschen";
$string['confirmtogglevisibility'] = "Ändere die Sichtbarkeit";
$string['confirmtogglevisibilitybody'] = "Wenn Sie die Sichtbarkeit ändern sind die Einträge
    nur noch für Userinnen mit entsprechenden Bearbeitungsrechten sichtbar.";
$string['confirmtogglevisibilitytitle'] = "Wollen Sie wirklich die Sichtbarkeit ändern?";
$string['contacts'] = 'Contacts';
$string['contacts_givenname'] = 'Given name';
$string['contacts_mail'] = 'E-Mail';
$string['contacts_surname'] = 'Surname';
$string['contactsupport'] = "Kontaktiere Support";
$string['createsupportticket'] = 'Supportticket anlegen';
$string['createticket'] = 'Ticket erstellen';
$string['debug'] = 'Debug Modus';
$string['debug_desc'] = 'Debug Modus hilf beim Finden von Fehlern, zum Beispiel indem die jwt tokens ausgegeben werden, statt direkt weiterzuleiten.';
$string['delete'] = "Delete";
$string['displayfaqs'] = "Zeige FAQs";
$string['draweropen'] = "Block offen nach login";
$string['draweropen_desc'] = "Mit dieser Einstellung kann erzwungen werden, dass der Block auf der rechten Seite nach jedem neuerlichen Login offen ist.";
$string['edit'] = "Edit";
$string['edit_details'] = 'Edit details';
$string['edit_entity'] = 'Edit Entity';
$string['edit_image'] = 'Edit image';
$string['editcategories'] = "Kategorien editieren";
$string['editquestions'] = "Fragen editieren";
$string['employer'] = 'Arbeitgeber';
$string['entergroup'] = "Bite wählen Sie eine Gruppe";
$string['entermessage'] = "Ihre Nachricht ist zu kurz";
$string['entermodule'] = "Bite wählen Sie ein Modul";
$string['entertitle'] = "Bitte geben Sie einen Titel an";
$string['entity_category'] = 'Entity category';
$string['entity_description'] = 'Entity description';
$string['entity_name'] = 'Entity name';
$string['entity_order'] = 'Sort order';
$string['entity_parent'] = 'Entity parent';
$string['entity_title'] = 'Entity';
$string['entitysetup_heading'] = 'Edit or Create Entity';
$string['entrydeleted'] = "Eintrag wurde gelöscht";
$string['er_wb_faqname'] = "Ausgewählte Entität";
$string['faq'] = "FAQ";
$string['groups'] = "Gruppen";
$string['groupsnmodules'] = 'Gruppen und Module';
$string['high'] = "hoch";
$string['imagetoken'] = "Token um Bilder herunterzuladen.";
$string['input:content'] = 'Inhalt';
$string['input:parentid'] = 'Übergeordnete Kategorie';
$string['input:question'] = 'Frage';
$string['input:sortorder'] = 'Sortierung';
$string['input:title'] = 'Titel';
$string['input:type'] = 'Kategorie';
$string['invisible'] = "Sichtbar";
$string['jwtapp'] = "JWT App";
$string['jwtsecret'] = "JWT Secret";
$string['low'] = "niedrig";
$string['mapstrings'] = 'Ändere Breadcrumb Namen mit einer JSON Zuordnung e.g. {"Cat1": "User"}';
$string['medium'] = "mittel";
$string['message'] = "Nachricht";
$string['minmessagelength'] = 'Minimale Länge einer Support Nachricht';
$string['modules'] = "Module";
$string['new_entity'] = 'New Entity';
$string['nocourseid'] = "Bitte wählen Sie einen Kurs aus, dem dieser Eintrag zugeordnet ist";
$string['nocourseselected'] = "Kein Kurs ausgewählt";
$string['none'] = 'None';
$string['noresult'] = "Kein Ergebnis gefunden.";
$string['normal'] = "normal";
$string['orphanentries'] = "Verweiste Einträge";
$string['pleasechoose'] = "Bitte wählen Sie";
$string['pluginname'] = 'FAQ';
$string['priority'] = "Priorität";
$string['privacy:metadata'] = 'Das FAQ-Plugin speichert keine personenbezogenen Daten.';
$string['resturl'] = "Url für externes Support system";
$string['resturl_desc'] = "Die Supportanfrage kann an ein externes System weitergeleitet werden.";
$string['root'] = 'Oberste Ebene';
$string['saveerror'] = "Fehler beim Speichern";
$string['savesuccess'] = "Erfolgreich gespeichert";
$string['search:faqentry'] = 'FAQ - entry';
$string['searchcategory'] = '... durchsuche Kategorien';
$string['searchfaq'] = "... durchsuche FAQs";
$string['searchfaqs'] = "FAQs";
$string['stringtooshort'] = "Eingabe zu kurz...";
$string['support'] = 'Support';
$string['supportactioncreate'] = 'Anfrage';
$string['supportanfrage'] = 'Support Anfrage';
$string['supportmessagebaseurl'] = "Support Nachricht Basis-URL";
$string['supportmytickets'] = 'Meine Tickets <i class="fa fa-angle-double-right" aria-hidden="true"></i>';
$string['supportonlineticket'] = 'Online-Ticket';
$string['supportstoerung'] = 'Störung melden <i class="fa fa-angle-double-right" aria-hidden="true"></i>';
$string['supportvertrieb'] = 'Anfrage an den Vertrieb <i class="fa fa-angle-double-right" aria-hidden="true"></i>';
$string['supportweiterbildung'] = 'Aus- und Weiterbildung <i class="fa fa-angle-double-right" aria-hidden="true"></i>';
$string['team'] = 'Comm-Unity Team <i class="fa fa-angle-double-right" aria-hidden="true"></i>';
$string['thankyou'] = "Danke";
$string['thankyouforsupportmessage'] = "Geben Sie hier den Text ein, um Ihren NutzerInnen für Supportanfragen zu danken.";
$string['thankyoutext'] = "Danke für ihre Nachricht! Unser Support wird sich mit Ihnen in Verbindung setzen!";
$string['title'] = "Titel";
$string['usesupport'] = "Zeige Support Link in der Navigationsleiste";
$string['usesupport_desc'] = "Der Support Link gibt einfachen Zugang zu den FAQs";
$string['view'] = "View";
$string['wb_faq:accessimages'] = "Zugriff auf Bilder";
$string['wb_faq:canedit'] = 'Verwalte FAQ';
$string['wb_faq:cansendsupportmessage'] = "Kann Supportnachricht verschicken";
$string['wb_faq:copymodule'] = "wb_faq: copy module";
$string['writemessage'] = "Nachricht";
