@local @local_wb_faq @javascript
Feature: Search the FAQ
  In order to find an answer quickly
  As a user
  I want to search all categories without reloading the page

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | One      | student1@example.com |
    And the following "local_wb_faq > categories" exist:
      | title   |
      | Network |
      | Hidden  |
    And the following "local_wb_faq > categories" exist:
      | title | parent  |
      | Wi-Fi | Network |
    And the following "local_wb_faq > questions" exist:
      | title            | parent | content                | enabled |
      | Router restart   | Wi-Fi  | <p>Überprüfung nötig</p> | 1       |
      | Secret router    | Wi-Fi  | <p>Hidden answer</p>   | 0       |
    And I log in as "student1"
    And I open the FAQ page "index"

  Scenario: Search finds questions in subcategories, but no hidden ones
    Given I set the FAQ page reload marker
    When I set the field "Search FAQ" to "router"
    Then I should see "Router restart"
    And I should not see "Secret router"
    And I should see "Search results: 1"
    And the FAQ page should not have been reloaded
    And the FAQ page url should contain "faqq=router"

  Scenario: Search also finds umlauts written differently
    When I set the field "Search FAQ" to "Ueberpruefung"
    Then I should see "Router restart"

  Scenario: Too short search terms and missing results are explained
    When I set the field "Search FAQ" to "ro"
    Then I should see "Please enter at least 3 characters."
    When I set the field "Search FAQ" to "nothingtofind"
    Then I should see "No results found."

  Scenario: Clearing the search shows the category again
    When I set the field "Search FAQ" to "router"
    Then I should see "Router restart"
    When I set the field "Search FAQ" to ""
    Then I should see "Network"
    And I should not see "Router restart"
