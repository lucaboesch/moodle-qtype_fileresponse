@qtype @qtype_fileresponse
Feature: Test all the basic functionality of the file response question type
  In order to evaluate students responses, As a teacher I need to
  create and preview fileresponse questions.

  Background:
    Given the following "users" exist:
      | username |
      | teacher  |
      | student  |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "activities" exist:
      | activity | name   | intro        | course | idnumber |
      | quiz     | Quiz 1 | Quiz 1 intro | C1     | quiz1    |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | teacher | C1     | editingteacher |
      | student | C1     | student        |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "blocks" exist:
      | blockname     | contextlevel | reference | pagetypepattern | defaultregion |
      | private_files | System       | 1         | my-index        | side-post     |

  @javascript @_file_upload
  Scenario: Create and then attempt a fileresponse question disallowing filedownload and disallowing file picker plugins for Moodle ≤ 4.5
    Given the site is running Moodle version 4.5 or lower
    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    And I add a "File Response" question filling the form with:
      | Question name                             | File Response 001                       |
      | Question text                             | Upload a PDF file, please.              |
      | General feedback                          | This is general feedback                |
      | File click context menu and File download | Rename, Delete (file download disabled) |
      | File picker plugins                       | Disable ("Upload a file" only)          |
    Then I should see "File Response 001"
    And I am on the "Quiz 1" "mod_quiz > Edit" page
    And I open the "last" add to quiz menu
    And I follow "from question bank"
    And I set the field with xpath "//input[@type='checkbox' and @id='qbheadercheckbox']" to "1"
    And I press "Add selected questions to the quiz"
    Then I should see "File Response 001" on quiz page "1"
    And I log out
    When I am on the "Quiz 1" "mod_quiz > View" page logged in as "student"
    And I press "Attempt quiz"
    And I click on "Add..." "button"
    Then I should see "Upload a file"
    And I should not see "Private files"

  @javascript @_file_upload
  Scenario: Create and then attempt a fileresponse question disallowing filedownload and disallowing file picker plugins for Moodle ≥ 5.0
    Given the site is running Moodle version 5.0 or higher

    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    And I add a "File Response" question filling the form with:
      | Question name                             | File Response 001                       |
      | Question text                             | Upload a PDF file, please.              |
      | General feedback                          | This is general feedback                |
      | File click context menu and File download | Rename, Delete (file download disabled) |
      | File picker plugins                       | Disable ("Upload a file" only)          |
    Then I should see "File Response 001"
    And I am on the "Quiz 1" "mod_quiz > Edit" page
    And I open the "last" add to quiz menu
    And I follow "from question bank"
    And I click on "Switch bank" "button"
    And I click on "C1 - System shared question bank" "link" in the "Select question bank" "dialogue"
    And I set the field with xpath "//input[@type='checkbox' and @id='qbheadercheckbox']" to "1"
    And I press "Add selected questions to the quiz"
    Then I should see "File Response 001" on quiz page "1"
    And I log out
    When I am on the "Quiz 1" "mod_quiz > View" page logged in as "student"
    And I press "Attempt quiz"
    And I click on "Add..." "button"
    Then I should see "Upload a file"
    And I should not see "Private files"

  @javascript @_file_upload
  Scenario: Create and then attempt a fileresponse question allowing filedownload and allowing file picker plugins for Moodle ≤ 4.5
    Given the site is running Moodle version 4.5 or lower

    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    And I add a "File Response" question filling the form with:
      | Question name                             | File Response 001                                      |
      | Question text                             | Upload a PDF file, please.                             |
      | General feedback                          | This is general feedback                               |
      | File click context menu and File download | Download, Rename, Move, Delete (file download enabled) |
      | File picker plugins                       | Enable (also Flickr, Wikimedia etc.)                   |
    Then I should see "File Response 001"
    And I am on the "Quiz 1" "mod_quiz > Edit" page
    And I open the "last" add to quiz menu
    And I follow "from question bank"
    And I set the field with xpath "//input[@type='checkbox' and @id='qbheadercheckbox']" to "1"
    And I press "Add selected questions to the quiz"
    Then I should see "File Response 001" on quiz page "1"
    And I log out
    When I log in as "student"
    And I follow "Manage private files..."
    And I upload "question/type/fileresponse/tests/fixtures/testfile.txt" file to "Files" filemanager
    And I press "Save changes"
    And I am on the "Quiz 1" "mod_quiz > View" page logged in as "student"
    And I press "Attempt quiz"
    And I click on "Add..." "button"
    Then I should see "Upload a file"
    And I should see "Private files"
    And I click on "Private files" "link" in the ".fp-repo-area" "css_element"
    And I click on "testfile.txt" "link"
    And I click on "Select this file" "button"
    And I click on "testfile.txt" "link"
    Then I should see "Download"

  @javascript @_file_upload
  Scenario: Create and then attempt a fileresponse question allowing filedownload and allowing file picker plugins for Moodle ≥ 5.0
    Given the site is running Moodle version 5.0 or higher

    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    And I add a "File Response" question filling the form with:
      | Question name                             | File Response 001                                      |
      | Question text                             | Upload a PDF file, please.                             |
      | General feedback                          | This is general feedback                               |
      | File click context menu and File download | Download, Rename, Move, Delete (file download enabled) |
      | File picker plugins                       | Enable (also Flickr, Wikimedia etc.)                   |
    Then I should see "File Response 001"
    And I am on the "Quiz 1" "mod_quiz > Edit" page
    And I open the "last" add to quiz menu
    And I follow "from question bank"
    And I click on "Switch bank" "button"
    And I click on "C1 - System shared question bank" "link" in the "Select question bank" "dialogue"
    And I set the field with xpath "//input[@type='checkbox' and @id='qbheadercheckbox']" to "1"
    And I press "Add selected questions to the quiz"
    Then I should see "File Response 001" on quiz page "1"
    And I log out
    When I log in as "student"
    And I follow "Manage private files..."
    And I upload "question/type/fileresponse/tests/fixtures/testfile.txt" file to "Files" filemanager
    And I press "Save changes"
    And I am on the "Quiz 1" "mod_quiz > View" page logged in as "student"
    And I press "Attempt quiz"
    And I click on "Add..." "button"
    Then I should see "Upload a file"
    And I should see "Private files"
    And I click on "Private files" "link" in the ".fp-repo-area" "css_element"
    And I click on "testfile.txt" "link"
    And I click on "Select this file" "button"
    And I click on "testfile.txt" "link"
    Then I should see "Download"

  @javascript @_file_upload
  Scenario: Create and then attempt a fileresponse question disallowing filedownload and allowing file picker plugins for Moodle ≤ 4.5
    Given the site is running Moodle version 4.5 or lower

    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    And I add a "File Response" question filling the form with:
      | Question name                             | File Response 001                       |
      | Question text                             | Upload a PDF file, please.              |
      | General feedback                          | This is general feedback                |
      | File click context menu and File download | Rename, Delete (file download disabled) |
      | File picker plugins                       | Enable (also Flickr, Wikimedia etc.)    |
    Then I should see "File Response 001"
    And I am on the "Quiz 1" "mod_quiz > Edit" page
    And I open the "last" add to quiz menu
    And I follow "from question bank"
    And I set the field with xpath "//input[@type='checkbox' and @id='qbheadercheckbox']" to "1"
    And I press "Add selected questions to the quiz"
    Then I should see "File Response 001" on quiz page "1"
    And I log out
    When I log in as "student"
    And I follow "Manage private files..."
    And I upload "question/type/fileresponse/tests/fixtures/testfile.txt" file to "Files" filemanager
    And I press "Save changes"
    And I am on the "Quiz 1" "mod_quiz > View" page logged in as "student"
    And I press "Attempt quiz"
    And I click on "Add..." "button"
    Then I should see "Upload a file"
    And I should see "Private files"
    And I click on "Private files" "link" in the ".fp-repo-area" "css_element"
    And I click on "testfile.txt" "link"
    And I click on "Select this file" "button"
    And I click on "testfile.txt" "link"
    Then I should not see "Download"

  @javascript @_file_upload
  Scenario: Create and then attempt a fileresponse question disallowing filedownload and allowing file picker plugins for Moodle ≥ 5.0
    Given the site is running Moodle version 5.0 or higher

    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    And I add a "File Response" question filling the form with:
      | Question name                             | File Response 001                       |
      | Question text                             | Upload a PDF file, please.              |
      | General feedback                          | This is general feedback                |
      | File click context menu and File download | Rename, Delete (file download disabled) |
      | File picker plugins                       | Enable (also Flickr, Wikimedia etc.)    |
    Then I should see "File Response 001"
    And I am on the "Quiz 1" "mod_quiz > Edit" page
    And I open the "last" add to quiz menu
    And I follow "from question bank"
    And I click on "Switch bank" "button"
    And I click on "C1 - System shared question bank" "link" in the "Select question bank" "dialogue"
    And I set the field with xpath "//input[@type='checkbox' and @id='qbheadercheckbox']" to "1"
    And I press "Add selected questions to the quiz"
    Then I should see "File Response 001" on quiz page "1"
    And I log out
    When I log in as "student"
    And I follow "Manage private files..."
    And I upload "question/type/fileresponse/tests/fixtures/testfile.txt" file to "Files" filemanager
    And I press "Save changes"
    And I am on the "Quiz 1" "mod_quiz > View" page logged in as "student"
    And I press "Attempt quiz"
    And I click on "Add..." "button"
    Then I should see "Upload a file"
    And I should see "Private files"
    And I click on "Private files" "link" in the ".fp-repo-area" "css_element"
    And I click on "testfile.txt" "link"
    And I click on "Select this file" "button"
    And I click on "testfile.txt" "link"
    Then I should not see "Download"
