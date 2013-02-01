Feature: Treeblogs plugin standart features BDD
    Test base functionality of LiveStreet Treeblogs plugin (duscussed plugins)

@mink:selenium2
    Scenario: Treeblog Blog tests
        Then check is plugin active "treeblogs"
        Given I load fixtures for plugin "treeblogs"

        Given I am on "/login"
        Then I want to login as "admin"

        Given I am on "/index/discussed/"
        Then I should see element "#content" values in order:
          | value |
          | second test topic</a> |
          | first test topic</a> |


        Then I follow "first test topic"
        And I wait "1000"
        And I follow "Add comment"
        And I fill in "comment_text" with "test comment"
        And I press "Add"
        Then I wait "1000"

        And I follow "Add comment"
        And I fill in "comment_text" with "test comment #2"
        And I press "Add"
        Then I wait "1000"

        Given I am on "/index/discussed/"
        Then I should see element "#content" values in order:
          | value |
          | first test topic</a> |
          | second test topic</a> |