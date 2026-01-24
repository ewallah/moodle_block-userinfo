@block @block_userinfo
Feature: Enable Block userinfo on the frontpage

  Background:
    Given the following "users" exist:
      | username |
      | student1 |
    And I log in as "admin"
    And I am on site homepage
    And I turn editing mode on
    And I add the "User Information block" block
    And I log out

  @javascript
  Scenario: Go to frontpage.
    Given I log in as "student1"
    And I am on site homepage
    Then I should see "Edit my profile"
    And I should see "Incomplete"

