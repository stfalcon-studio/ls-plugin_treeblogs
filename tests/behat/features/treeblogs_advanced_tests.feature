Feature: Treeblogs plugin standart features BDD
    Test base functionality of LiveStreet Treeblogs plugin (duscussed topics & blog list pagination)

@mink:selenium2
    Scenario: Treeblog Blog tests functionality of discussed topic list (sort order)
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
        Given I press element by css "#comment_id_0 a"

        And I fill in "comment_text" with "test comment"
        And I press "Add"
        Then I wait "1000"

        Given I press element by css "#comment_id_0 a"
        And I fill in "comment_text" with "test comment #2"
        And I press "Add"
        Then I wait "1000"

        Given I am on "/index/discussed/"
        Then I should see element "#content" values in order:
          | value |
          | first test topic</a> |
          | second test topic</a> |


@mink:selenium2
  Scenario: Treeblog testting for pagination in blogs list
    Then check is plugin active "treeblogs"
    Given I load fixtures for plugin "treeblogs"

    Given I am on "/login"
    Then I want to login as "admin"

    Given I am on "/blogs/"
    Then the response should not contain "pagination"

    Given I am on "/blog/add/"
    And I fill in "blog_title" with "blog#1"
    And I fill in "blog_url" with "blogurl1"
    And I fill in "blog_description" with "blog1 description text"
    And I press element by css "button[name='submit_blog_add']"
    And I wait "1000"

    Given I am on "/blogs/"
    And the response should contain "pagination"

    And I should see in element by css "blogs-list-original" values:
      | value |
      | Gadgets |
      | test1_level1 |
      | test1_level2 |
      | test2_level1 |
      | test2_level2 |
    And the response should contain ">2</a>"

    Then I follow "Last"
    And I wait "1000"
    And I should see in element by css "blogs-list-original" values:
      | value |
      | blog#1 |
    And the response should contain "pagination"
