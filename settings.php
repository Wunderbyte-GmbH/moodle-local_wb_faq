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
 * Plugin administration pages are defined here.
 *
 * @package     local_wb_faq
 * @category    admin
 * @copyright   2022 Wunderbyte GmbH<info@wunderbyte.at>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$componentname = 'local_wb_faq';

// Default for users that have site config.
if ($hassiteconfig) {
    // Add the category to the local plugin branch.
    $settings = new admin_settingpage($componentname . '_settings', '');

    $settings->add(
        new admin_setting_configcheckbox($componentname . '/accesonlyowncourses',
                get_string('accesonlyowncourses', 'local_wb_faq'),
                get_string('accesonlyowncourses_desc', 'local_wb_faq'), 1));

    $settings->add(
        new admin_setting_configtextarea('local_wb_faq/groupsnmodules',
            get_string('groupsnmodules', 'local_wb_faq'),
            '', '', PARAM_TEXT, 60, 10));

    $settings->add(
        new admin_setting_configtextarea('local_wb_faq/thankyouforsupportmessage',
            get_string('thankyouforsupportmessage', 'local_wb_faq'),
            '', '', PARAM_TEXT, 60, 10));

    $settings->add(
        new admin_setting_configtext('local_wb_faq/minmessagelength',
            get_string('minmessagelength', 'local_wb_faq'),
            '', 15, PARAM_INT));

    $settings->add(
        new admin_setting_configtext('local_wb_faq/supportmessagebaseurl',
            get_string('supportmessagebaseurl', 'local_wb_faq'),
            '', 15, PARAM_URL));

    $settings->add(
        new admin_setting_configtext('local_wb_faq/jwtsecret',
            get_string('jwtsecret', 'local_wb_faq'),
            '', 15, PARAM_TEXT));
    $settings->add(
        new admin_setting_configtext('local_wb_faq/jwtapp',
            get_string('jwtapp', 'local_wb_faq'),
            '', 15, PARAM_TEXT));

    $settings->add(
        new admin_setting_configcheckbox($componentname . '/usesupport',
                get_string('usesupport', 'local_wb_faq'),
                get_string('usesupport_desc', 'local_wb_faq'), 0));

    $settings->add(
        new admin_setting_configcheckbox($componentname . '/debug',
                get_string('debug', 'local_wb_faq'),
                get_string('debug_desc', 'local_wb_faq'), 0));

    $ADMIN->add('localplugins', new admin_category($componentname, get_string('pluginname', $componentname)));
    $ADMIN->add($componentname, $settings);
}
