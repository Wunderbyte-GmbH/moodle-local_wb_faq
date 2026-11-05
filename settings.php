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
 * Settings of the plugin.
 *
 * @package    local_wb_faq
 * @copyright  2022 Wunderbyte GmbH <info@wunderbyte.at>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_wb_faq_settings', get_string('settings'));

    $settings->add(new admin_setting_configcheckbox(
        'local_wb_faq/allowanonymous',
        get_string('allowanonymous', 'local_wb_faq'),
        get_string('allowanonymous_desc', 'local_wb_faq'),
        0
    ));
    $settings->add(new admin_setting_configcheckbox(
        'local_wb_faq/accesonlyowncourses',
        get_string('accesonlyowncourses', 'local_wb_faq'),
        get_string('accesonlyowncourses_desc', 'local_wb_faq'),
        1
    ));
    $settings->add(new admin_setting_configtext(
        'local_wb_faq/perpage',
        get_string('perpage', 'local_wb_faq'),
        get_string('perpage_desc', 'local_wb_faq'),
        10,
        PARAM_INT
    ));
    $settings->add(new admin_setting_configtextarea(
        'local_wb_faq/mapstrings',
        get_string('mapstrings', 'local_wb_faq'),
        get_string('mapstrings_desc', 'local_wb_faq'),
        '',
        PARAM_RAW_TRIMMED
    ));
    $settings->add(new admin_setting_configcheckbox(
        'local_wb_faq/usesupport',
        get_string('usesupport', 'local_wb_faq'),
        get_string('usesupport_desc', 'local_wb_faq'),
        0
    ));
    $settings->add(new admin_setting_configtextarea(
        'local_wb_faq/supportlinks',
        get_string('supportlinks', 'local_wb_faq'),
        get_string('supportlinks_desc', 'local_wb_faq'),
        '',
        PARAM_RAW_TRIMMED
    ));
    $settings->add(new admin_setting_configcheckbox(
        'local_wb_faq/draweropen',
        get_string('draweropen', 'local_wb_faq'),
        get_string('draweropen_desc', 'local_wb_faq'),
        0
    ));

    $ADMIN->add('localplugins', new admin_category('local_wb_faq', get_string('pluginname', 'local_wb_faq')));
    $ADMIN->add('local_wb_faq', $settings);
    $ADMIN->add('local_wb_faq', new admin_externalpage(
        'local_wb_faq_manage',
        get_string('wb_faq:canedit', 'local_wb_faq'),
        new moodle_url('/local/wb_faq/wb_faq.php'),
        'local/wb_faq:canedit'
    ));
}
