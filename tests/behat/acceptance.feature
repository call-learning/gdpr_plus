@tool @tool_gdpr_plus
Feature: Accepting and reviewing acceptance later through the banner
  As a user I can accept all policies, accept only mandatory policies and review my choice later.
  Background:
    Given the following config values are set as admin:
      | sitepolicyhandler | tool_gdpr_plus |
    # This is required for now to prevent the overflow region affecting the action menus.
    And the following gdpr policies exist:
      | Name                | Revision | Content    | Summary     | Status | Optional | Audience |
      | This site policy    |          | full text2 | short text2 | active | 0        | all      |
      | This cookies policy |          | full text3 | short text3 | active | 1        | all      |
      | This privacy policy |          | full text4 | short text3 | active | 0        | loggedin |
    And the following "users" exist:
      | username | firstname | lastname | email           |
      | user1    | User      | One      | one@example.com |
      | user2    | User      | Two      | two@example.com |
      | manager  | Max       | Manager  | man@example.com |
    And the following "role assigns" exist:
      | user    | role    | contextlevel | reference |
      | manager | manager | System       |           |

  @javascript
  Scenario: Accept policy on login as guest and keep the settings as is when navigating pages
    And I am on site homepage
    And I should see "If you want to continue browsing this website, you need to agree to some of our policies."
    And I click on "Show settings" "button"
    Then I should see "This site policy"
    And I should see "This cookies policy"
    And I should not see "This privacy policy"
    And "input[name=\"this-site-policy\"][checked]" "css_element" should exist
    And "input[name=\"this-cookies-policy\"]" "css_element" should exist
    And "input[name=\"this-cookies-policy\"][checked]" "css_element" should not exist
    And "input[name=\"this-privacy-policy\"]" "css_element" should not exist
    When I click on "This site policy" "link"
    Then I should see "full text2"
    And I follow "Log in"
    When I press "Log in as a guest"
    And I should see "If you want to continue browsing this website, you need to agree to some of our policies."
    # Confirm when navigating, the pop-up policies are displayed.
    When I follow "Home"
    And I should see "If you want to continue browsing this website, you need to agree to some of our policies."
    And I click on "Show settings" "button"
    Then I should see "This site policy"
    And I should see "This cookies policy"
    And I should not see "This privacy policy"
    Then I click on "input[name=\"this-cookies-policy\"]" "css_element"
    Then I click on "Save my choices" "button"
    When I reload the page
    Then I should not see "If you want to continue browsing this website, you need to agree to some of our policies"
    # A link but with button role
    When I click on "Show Policies" "button"
    And I should see "If you want to continue browsing this website, you need to agree to some of our policies."
    Then I click on "Show settings" "button"
    Then I should see "This site policy"
    And I should see "This cookies policy"
    And I should not see "This privacy policy"
    And "input[name=\"this-site-policy\"][checked]" "css_element" should exist
    And "input[name=\"this-cookies-policy\"][checked]" "css_element" should exist

  @javascript
  Scenario: Accept policy without login in and keep the settings as is when navigating pages
    And I am on site homepage
    And I should see "If you want to continue browsing this website, you need to agree to some of our policies."
    And I click on "Show settings" "button"
    Then I should see "This site policy"
    And I should see "This cookies policy"
    And I should not see "This privacy policy"
    And "input[name=\"this-site-policy\"][checked]" "css_element" should exist
    And "input[name=\"this-cookies-policy\"]" "css_element" should exist
    And "input[name=\"this-cookies-policy\"][checked]" "css_element" should not exist
    And "input[name=\"this-privacy-policy\"]" "css_element" should not exist
    When I click on "This site policy" "link"
    Then I should see "full text2"
    # Confirm when navigating, the pop-up policies are displayed.
    Then I click on "Show settings" "button"
    Then I should see "This site policy"
    And I should see "This cookies policy"
    And I should not see "This privacy policy"
    Then I click on "input[name=\"this-cookies-policy\"]" "css_element"
    Then I click on "Save my choices" "button"
    When I reload the page
    Then I should not see "If you want to continue browsing this website, you need to agree to some of our policies"
    # A link but with button role
    When I click on "Show Policies" "button"
    And I should see "If you want to continue browsing this website, you need to agree to some of our policies."
    Then I click on "Show settings" "button"
    Then I should see "This site policy"
    And I should see "This cookies policy"
    And I should not see "This privacy policy"
    And "input[name=\"this-site-policy\"][checked]" "css_element" should exist
    And "input[name=\"this-cookies-policy\"][checked]" "css_element" should exist

  @javascript
  Scenario: Accept policy when login as user and I should be able to review it later.
    Given I log in as "user1"
    And I should see "This site policy"
    And I press "Next"
    And I should see "This cookies policy"
    And I press "Next"
    And I should see "This privacy policy"
    And I press "Next"
    And I set the field "I agree to the This site policy" to "1"
    And I set the field "No thanks, I decline This cookies policy" to "1"
    And I set the field "I agree to the This privacy policy" to "1"
    And I press "Next"
    And I am on site homepage
    And I should not see "If you want to continue browsing this website, you need to agree to some of our policies."
    # A link but with button role
    When I click on "Show Policies" "button"
    And I should see "If you want to continue browsing this website, you need to agree to some of our policies."
    Then I click on "Show settings" "button"
    Then I should see "This site policy"
    And I should see "This cookies policy"
    And I should see "This privacy policy"
    And "input[name=\"this-site-policy\"][checked]" "css_element" should exist
    And "input[name=\"this-privacy-policy\"][checked]" "css_element" should exist
    And "input[name=\"this-cookies-policy\"][checked]" "css_element" should not exist

