@local @local_wb_faq @javascript
Feature: Access to the FAQ
  In order to protect restricted content
  As an administrator
  I want to control who can read which part of the FAQ

  Background:
    # Moodle 5.2 turns on forcelogin in new installations, which would override the setting of the plugin.
    Given the following config values are set as admin:
      | forcelogin | 0 |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | One      | student1@example.com |
      | student2 | Student   | Two      | student2@example.com |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | student1 | C1     | student |
    And the following "local_wb_faq > categories" exist:
      | title  |
      | Public |
    And the following "local_wb_faq > categories" exist:
      | title      | course |
      | Restricted | C1     |
    And the following "local_wb_faq > questions" exist:
      | title           | parent | content            |
      | Public question | Public | <p>Public answer</p> |

  Scenario: Users who are not logged in have to log in by default
    When I open the FAQ page "index"
    Then I should see "Log in"
    And "input[name='username']" "css_element" should exist

  Scenario: Users who are not logged in can read public categories if this is allowed
    Given the following config values are set as admin:
      | allowanonymous | 1 | local_wb_faq |
    When I open the FAQ page "index"
    And I set the FAQ page reload marker
    Then I should see "Public"
    And I should not see "Restricted"
    When I click on "Public" "link" in the "[data-region='faq-content']" "css_element"
    And I click on "Public question" "link"
    Then I should see "Public answer"
    And the FAQ page should not have been reloaded
    When I set the field "Search FAQ" to "Public question"
    Then I should see "Search results: 1"

  Scenario: Course restricted categories are only shown to users of the course
    Given I log in as "student1"
    When I open the FAQ page "index"
    Then I should see "Restricted"
    And I log out
    And I log in as "student2"
    When I open the FAQ page "index"
    Then I should see "Public"
    And I should not see "Restricted"
