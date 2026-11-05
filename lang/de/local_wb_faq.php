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

$string['accesonlyowncourses'] = 'Zugriff auf eigene Kurse beschränken';
$string['accesonlyowncourses_desc'] = 'Kategorien mit Kursbezug werden nur Nutzer/innen mit Zugriff auf diesen Kurs angezeigt. Kategorien ohne Kurs werden immer angezeigt.';
$string['addcategory'] = 'Kategorie hinzufügen';
$string['addquestion'] = 'Frage hinzufügen';
$string['allowanonymous'] = 'Zugriff ohne Anmeldung erlauben';
$string['allowanonymous_desc'] = 'Nicht angemeldete Nutzer/innen können alle Kategorien ohne Kursbezug lesen. Keine Wirkung, wenn die Website eine Anmeldung erzwingt.';
$string['breadcrumbs'] = 'FAQ-Navigation';
$string['categorynotfound'] = 'Kategorie "{$a}" wurde nicht gefunden';
$string['choosecourse'] = 'Kurs. Nur Nutzer/innen mit Zugriff auf diesen Kurs sehen diese Kategorie und ihren gesamten Inhalt.';
$string['confirmdeleteentry'] = 'Löschen';
$string['confirmdeleteentrybody'] = 'Möchten Sie diesen Eintrag wirklich löschen? Bei einer Kategorie wird auch ihr gesamter Inhalt gelöscht.';
$string['confirmdeleteentrytitle'] = 'Löschen bestätigen';
$string['confirmtogglevisibility'] = 'Sichtbarkeit ändern';
$string['confirmtogglevisibilitybody'] = 'Einen verborgenen Eintrag sehen nur noch Nutzer/innen, die die FAQ bearbeiten dürfen.';
$string['confirmtogglevisibilitytitle'] = 'Möchten Sie die Sichtbarkeit wirklich ändern?';
$string['displayfaqs'] = 'FAQs anzeigen';
$string['draweropen'] = 'Block-Leiste nach der Anmeldung öffnen';
$string['draweropen_desc'] = 'Mit dieser Einstellung wird die Block-Leiste auf der rechten Seite nach jeder neuen Anmeldung geöffnet.';
$string['editcategory'] = 'Kategorie bearbeiten';
$string['editquestion'] = 'Frage bearbeiten';
$string['entrydeleted'] = 'Eintrag wurde gelöscht';
$string['entrynotfound'] = 'Dieser Eintrag existiert nicht oder Sie haben keinen Zugriff darauf.';
$string['entryvisible'] = 'Sichtbar';
$string['faq'] = 'FAQ';
$string['faqentryadded'] = 'FAQ-Eintrag hinzugefügt';
$string['faqentrydeleted'] = 'FAQ-Eintrag gelöscht';
$string['faqentryupdated'] = 'FAQ-Eintrag aktualisiert';
$string['hiddenentry'] = 'Verborgen';
$string['input:content'] = 'Inhalt';
$string['input:parentid'] = 'Übergeordnete Kategorie';
$string['input:question'] = 'Frage';
$string['input:title'] = 'Titel';
$string['invalidorder'] = 'Diese Einträge können nicht gemeinsam sortiert werden.';
$string['invalidparent'] = 'Eine Kategorie kann nicht in sich selbst oder in eine ihrer Unterkategorien verschoben werden.';
$string['loginrequired'] = 'Bitte melden Sie sich an, um die FAQ zu lesen.';
$string['mapstrings'] = 'Bezeichnungen im Navigationspfad';
$string['mapstrings_desc'] = 'Ersetzt Kategorienamen im Navigationspfad, als JSON-Zuordnung, z. B. {"Kat1": "Nutzer"}';
$string['moveentry'] = 'Eintrag verschieben';
$string['nocourseselected'] = 'Kein Kurs';
$string['noentries'] = 'Es gibt noch keine Einträge.';
$string['noresult'] = 'Keine Ergebnisse gefunden.';
$string['pagination'] = 'Seiten';
$string['perpage'] = 'Einträge pro Seite';
$string['perpage_desc'] = 'Anzahl der Fragen oder Suchergebnisse auf einer Seite.';
$string['pluginname'] = 'FAQ';
$string['privacy:metadata'] = 'Das FAQ-Plugin speichert keine personenbezogenen Daten.';
$string['root'] = 'Oberste Ebene';
$string['savesuccess'] = 'Erfolgreich gespeichert';
$string['search:faqentry'] = 'FAQ - Eintrag';
$string['searchfaq'] = 'FAQ durchsuchen';
$string['searchresults'] = 'Suchergebnisse: {$a}';
$string['stringtooshort'] = 'Bitte geben Sie mindestens {$a} Zeichen ein.';
$string['support'] = 'Hilfe';
$string['supportlinks'] = 'Links im Hilfe-Menü';
$string['supportlinks_desc'] = 'Weitere Einträge des Hilfe-Menüs, einer pro Zeile im Format Bezeichnung|URL.';
$string['togglevisibility'] = 'Sichtbarkeit ändern';
$string['usesupport'] = 'Hilfe-Menü in der Navigationsleiste anzeigen';
$string['usesupport_desc'] = 'Das Hilfe-Menü gibt angemeldeten Nutzer/innen von jeder Seite aus Zugriff auf die FAQ.';
$string['wb_faq:canedit'] = 'FAQ verwalten';
