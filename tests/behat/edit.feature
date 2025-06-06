@qtype @qtype_fileresponse @qtype_fileresponse_edit
Feature: Test editing a file response question
  As a teacher
  In order to be able to update my file response questions
  I need to edit them

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email               |
      | teacher1 | T1        | Teacher1 | teacher1@moodle.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype        | name          |
      | Test questions   | fileresponse | File Response |

  @javascript
  Scenario: Edit a fileresponse question
    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher1
    When I choose "Edit question" action for "File Response" in the question bank
    And I set the following fields to these values:
      | Question name | Edited question name |
    And I press "id_submitbutton"
    Then I should see "Edited question name"
    And I choose "Edit question" action for "Edited question name" in the question bank
