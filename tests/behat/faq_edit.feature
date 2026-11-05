@local @local_wb_faq @javascript
Feature: Edit the FAQ
  In order to provide answers
  As a manager
  I want to add and change entries without reloading the page

  Background:
    Given I log in as "admin"

  Scenario: The first category and question can be added to an empty FAQ
    Given I open the FAQ page "wb_faq"
    And I set the FAQ page reload marker
    And I should see "There are no entries yet."
    When I click on "Add category" "link"
    And I set the field "Title" to "First category"
    And I click on "Save changes" "button" in the ".modal-dialog" "css_element"
    Then I should see "First category"
    When I click on "First category" "link" in the "[data-region='faq-content']" "css_element"
    And I click on "Add question" "link"
    And I set the field "Question" to "First question"
    And I set the field "Content" to "First answer"
    And I click on "Save changes" "button" in the ".modal-dialog" "css_element"
    Then I should see "First question"
    When I click on "First question" "link"
    Then I should see "First answer"
    And the FAQ page should not have been reloaded

  Scenario: Edit, hide and delete a question
    Given the following "local_wb_faq > questions" exist:
      | title        |
      | Old question |
    And I open the FAQ page "wb_faq"
    And I set the FAQ page reload marker
    When I click on "Edit question" "link" in the "Old question" "list_item"
    And I set the field "Question" to "New question"
    And I click on "Save changes" "button" in the ".modal-dialog" "css_element"
    Then I should see "New question"
    And I should not see "Old question"
    When I click on "Change visibility" "link" in the "New question" "list_item"
    And I click on "Change visibility" "button" in the ".modal-dialog" "css_element"
    Then I should see "Hidden" in the "New question" "list_item"
    When I click on "Delete" "link" in the "New question" "list_item"
    And I click on "Delete" "button" in the ".modal-dialog" "css_element"
    Then I should not see "New question"
    And the FAQ page should not have been reloaded

  Scenario: Questions can be reordered by drag and drop
    Given the following "local_wb_faq > questions" exist:
      | title           | sortorder |
      | Question first  | 2         |
      | Question second | 1         |
    And I open the FAQ page "wb_faq"
    And "Question first" "text" should appear before "Question second" "text"
    When I drag "[data-region='faq-entry']:last-child [data-drag-type='move']" "css_element" and I drop it in "[data-region='faq-entry']:first-child" "css_element"
    And I wait "1" seconds
    And I reload the page
    Then "Question second" "text" should appear before "Question first" "text"

  Scenario: Users without the capability get no edit controls
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | One      | student1@example.com |
    And I log out
    And I log in as "student1"
    When I open the FAQ page "wb_faq"
    Then I should not see "Add category"
    And I should not see "Add question"
