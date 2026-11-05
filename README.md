# Local wb_faq #

## Installing via uploaded ZIP file ##

1. Log in to your Moodle site as an admin and go to _Site administration >
   Plugins > Install plugins_.
2. Upload the ZIP file with the plugin code. You should only be prompted to add
   extra details if your plugin type is not automatically detected.
3. Check the plugin validation report and finish the installation.

## Installing manually ##

The plugin can be also installed by putting the contents of this directory to

    {your/moodle/dirroot}/local/wb_faq

Afterwards, log in to your Moodle site as an admin and go to _Site administration >
Notifications_ to complete the installation.

Alternatively, you can run

    $ php admin/cli/upgrade.php

to complete the installation from the command line.

## Usage ##

The FAQ is available at `/local/wb_faq/index.php`. Users with the capability `local/wb_faq:canedit` manage it at
`/local/wb_faq/wb_faq.php`, which is linked from the FAQ and from the plugin settings.

It can be embedded with the shortcode `[faq]`, which needs the plugin `filter_shortcodes`. Optional attributes are
`cat="Name of the category"`, `nosearch=1`, `nolist=1` and `allowedit=1`.

Links keep their state in the parameters `faqcat`, `faqentry`, `faqpage` and `faqq`.

## Settings ##

* **Allow access without login**: Users who are not logged in can read all categories without a course.
* **Restrict access to own courses**: Categories linked to a course are only shown to users of this course.
* **Entries per page**, **Breadcrumb labels**, **Help menu** and its **links**, **Open block drawer after login**.

## License ##

2022 Wunderbyte GmbH <info@wunderbyte.at>

This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program.  If not, see <https://www.gnu.org/licenses/>.
