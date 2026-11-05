@local @local_wb_faq @javascript
Feature: Move through the FAQ without page reloads
  In order to find an answer quickly
  As a user
  I want to open categories, questions and pages without reloading the page

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | One      | student1@example.com |
    And the following "local_wb_faq > categories" exist:
      | title   |
      | Network |
      | Account |
    And the following "local_wb_faq > categories" exist:
      | title | parent  |
      | Wi-Fi | Network |
    And the following "local_wb_faq > questions" exist:
      | title            | parent  | content               |
      | Is the cable ok? | Network | <p>Check the plug</p> |
      | How do I log in? | Account | <p>Use your account</p> |
    And I log in as "student1"

  Scenario: Open a category and a question
    Given I open the FAQ page "index"
    And I set the FAQ page reload marker
    When I click on "Network" "link" in the "[data-region='faq-content']" "css_element"
    Then I should see "Wi-Fi"
    And I should see "Is the cable ok?"
    And I should not see "Account"
    And I should not see "Check the plug"
    When I click on "Is the cable ok?" "link"
    Then I should see "Check the plug"
    And the FAQ page should not have been reloaded
    And the FAQ page url should contain "faqentry="

  Scenario: The browser history works
    Given I open the FAQ page "index"
    And I set the FAQ page reload marker
    When I click on "Network" "link" in the "[data-region='faq-content']" "css_element"
    Then I should see "Wi-Fi"
    And the FAQ page url should contain "faqcat="
    When I go back in the FAQ browser history
    Then I should see "Account"
    And I should not see "Wi-Fi"
    And the FAQ page should not have been reloaded

  Scenario: Deep links show the requested state
    When I open the FAQ deep link to category "Network"
    Then I should see "Wi-Fi"
    And I should not see "Account"
    When I open the FAQ deep link to question "How do I log in?"
    Then I should see "Use your account"

  Scenario: The FAQ works the same way in the modern design
    Given the following config values are set as admin:
      | design | modern | local_wb_faq |
    And I open the FAQ page "index"
    And I set the FAQ page reload marker
    And ".wb-faq-design-modern" "css_element" should exist
    When I click on "Network" "link" in the "[data-region='faq-content']" "css_element"
    Then I should see "Wi-Fi"
    When I click on "Is the cable ok?" "link"
    Then I should see "Check the plug"
    And ".wb-faq-design-modern" "css_element" should exist
    And the FAQ page should not have been reloaded

  Scenario: Long lists are paginated
    Given the following config values are set as admin:
      | perpage | 2 | local_wb_faq |
    And the following "local_wb_faq > questions" exist:
      | title      | parent  | sortorder |
      | Question A | Account | 9         |
      | Question B | Account | 8         |
      | Question C | Account | 7         |
    And I open the FAQ deep link to category "Account"
    And I set the FAQ page reload marker
    And I should see "Question A"
    And I should not see "Question C"
    When I click on "2" "link" in the ".wb-faq-pagination" "css_element"
    Then I should see "Question C"
    And I should not see "Question A"
    And the FAQ page should not have been reloaded
    And the FAQ page url should contain "faqpage=1"
