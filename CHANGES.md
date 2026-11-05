## Version 0.3.0 (2026110501)

### Improvements
* New setting `design`: choose between the classic and a new modern design.
* Real AJAX navigation: categories, questions, search and pages load only the changed fragment through webservices
  and the shared Mustache templates. Browser history and deep links (`faqcat`, `faqentry`, `faqpage`, `faqq`) work.
* The first state is rendered by the server. Links and the search form also work without JavaScript.
* Questions and search results are paginated (new setting `perpage`). Answers are loaded when a question is opened.
* New webservices `local_wb_faq_get_category`, `local_wb_faq_get_entry` and `local_wb_faq_search`.
* Code quality: separate data access, permissions, output and controllers, `declare(strict_types=1)`, moodle-cs clean.
* PHPUnit and Behat tests, GitHub Actions workflow.
* Works with Bootstrap 4 (Moodle 4.5) and Bootstrap 5 (Moodle 5.x).

### Bugfixes
* Fix #2: With an empty database, the buttons to add the first category and question are shown.
* Fix #3: Users who are not logged in or not enrolled saw too few categories. Categories without a course or linked
  to the site course are not restricted anymore. Reading without a login needs the new setting `allowanonymous`.
* Restrictions of a category now also apply to everything below it, including search and embedded files.
* Questions with files could not be created. Moving a category into its own subcategory is rejected.
* Deleting a category also deletes its content instead of leaving unreachable records behind.
* Drag and drop keeps the order of questions on other pages.
* Webservices which change data are declared as write functions and check the capability.

### Customer specific code

Removed:
* The ticket integration of one customer: `transfer.php`, `classes/support.php`, `classes/jwt.php`, `classes/rest.php`,
  `classes/issues.php`, the support message form, the audience `KOI`, the dependency on `local_groupmanager`
  (never declared), the hard coded menu entries "Vertrieb", "Aus- und Weiterbildung", "Störung", "Meine Tickets"
  and "Comm-Unity Team", and the webservice `local_wb_faq_provide_image`.
* With it: table `local_wb_faq_issues`, fields `module` and `supplement`, capabilities `cansendsupportmessage` and
  `accessimages`, and the settings `debug`, `groupsnmodules`, `imagetoken`, `jwtapp`, `jwtsecret`, `minmessagelength`,
  `resturl`, `supportmessagebaseurl` and `thankyouforsupportmessage`. **The upgrade deletes this data.**
* Demo data and unused pages: `admin.php`, `edit.php`, `filter.php`, the webservice `local_wb_faq_get_faq_data`.
* Hard coded German texts in templates ("Aktuelle Suche", "Kontakt"), "top Level", and a forced form margin.

Turned into settings:
* The menu entries are now the setting `supportlinks` (`Label|URL`, one per line).
* Access without login: `allowanonymous`. Entries per page: `perpage`.

Kept, as they already were settings: `accesonlyowncourses`, `mapstrings`, `usesupport`, `draweropen`.

## Version 0.1.0 (2022051900)
* Add Shortcodes
