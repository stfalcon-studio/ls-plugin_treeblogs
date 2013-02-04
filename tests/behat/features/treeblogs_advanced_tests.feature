Feature: Treeblogs plugin standart features BDD
    Test base functionality of LiveStreet Treeblogs plugin (duscussed topics & blog list pagination)

@mink:selenium2
    Scenario: Treeblog Blog tests functionality of discussed topic list
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

  @mink:selenium2
  Scenario: Treeblog testting for pagination in blogs controller (blogs list)
    Then check is plugin active "treeblogs"
    Given I load fixtures for plugin "treeblogs"

    Given I am on "/login"
    Then I want to login as "admin"

    Given I am on "/blog/add/"
    And I fill in "blog_title" with "blog#1"
    And I fill in "blog_url" with "blogurl1"
    And I fill in "blog_description" with "blog1 description text"
    And I press element by css "button[name='submit_blog_add']"
    And I wait "1000"

    Given I am on "/blog/add/"
    And I fill in "blog_title" with "blog#2"
    And I fill in "blog_url" with "blogurl2"
    And I fill in "blog_description" with "blog2 description text"
    And I press element by css "button[name='submit_blog_add']"
    And I wait "1000"

    Given I am on "/blog/add/"
    And I fill in "blog_title" with "blog#3"
    And I fill in "blog_url" with "blogurl3"
    And I fill in "blog_description" with "blog3 description text"
    And I press element by css "button[name='submit_blog_add']"
    And I wait "1000"

    Given I am on "/blog/add/"
    And I fill in "blog_title" with "blog#4"
    And I fill in "blog_url" with "blogurl4"
    And I fill in "blog_description" with "blog4 description text"
    And I press element by css "button[name='submit_blog_add']"
    And I wait "1000"

    Given I am on "/blog/add/"
    And I fill in "blog_title" with "blog#5"
    And I fill in "blog_url" with "blogurl5"
    And I fill in "blog_description" with "blog5 description text"
    And I press element by css "button[name='submit_blog_add']"
    And I wait "1000"

    Given I am on "/blog/add/"
    And I fill in "blog_title" with "blog#6"
    And I fill in "blog_url" with "blogurl6"
    And I fill in "blog_description" with "blog6 description text"
    And I press element by css "button[name='submit_blog_add']"
    And I wait "1000"

    Given I am on "/blog/add/"
    And I fill in "blog_title" with "blog#7"
    And I fill in "blog_url" with "blogurl7"
    And I fill in "blog_description" with "blog7 description text"
    And I press element by css "button[name='submit_blog_add']"
    And I wait "1000"

    Given I am on "/blog/add/"
    And I fill in "blog_title" with "blog#8"
    And I fill in "blog_url" with "blogurl8"
    And I fill in "blog_description" with "blog8 description text"
    And I press element by css "button[name='submit_blog_add']"
    And I wait "1000"

    Given I am on "/blog/add/"
    And I fill in "blog_title" with "blog#9"
    And I fill in "blog_url" with "blogurl9"
    And I fill in "blog_description" with "blog9 description text"
    And I press element by css "button[name='submit_blog_add']"
    And I wait "1000"

    Given I am on "/blog/add/"
    And I fill in "blog_title" with "blog#10"
    And I fill in "blog_url" with "blogurl10"
    And I fill in "blog_description" with "blog10 description text"
    And I press element by css "button[name='submit_blog_add']"
    And I wait "1000"

    Given I am on "/blog/add/"
    And I fill in "blog_title" with "blog#11"
    And I fill in "blog_url" with "blogurl11"
    And I fill in "blog_description" with "blog11 description text"
    And I press element by css "button[name='submit_blog_add']"
    And I wait "1000"

    Given I am on "/blog/add/"
    And I fill in "blog_title" with "blog#12"
    And I fill in "blog_url" with "blogurl12"
    And I fill in "blog_description" with "blog12 description text"
    And I press element by css "button[name='submit_blog_add']"
    And I wait "1000"

    Given I am on "/blog/add/"
    And I fill in "blog_title" with "blog#13"
    And I fill in "blog_url" with "blogurl13"
    And I fill in "blog_description" with "blog13 description text"
    And I press element by css "button[name='submit_blog_add']"
    And I wait "1000"

    Given I am on "/blog/add/"
    And I fill in "blog_title" with "blog#14"
    And I fill in "blog_url" with "blogurl14"
    And I fill in "blog_description" with "blog14 description text"
    And I press element by css "button[name='submit_blog_add']"
    And I wait "1000"

    Given I am on "/blog/add/"
    And I fill in "blog_title" with "blog#15"
    And I fill in "blog_url" with "blogurl15"
    And I fill in "blog_description" with "blog15 description text"
    And I press element by css "button[name='submit_blog_add']"
    And I wait "1000"

    Given I am on "/blog/add/"
    And I fill in "blog_title" with "blog#16"
    And I fill in "blog_url" with "blogurl16"
    And I fill in "blog_description" with "blog16 description text"
    And I press element by css "button[name='submit_blog_add']"

    Given I am on "/blogs/page2/"

    Then I should see in element by css "blogs-list-original" values:
    | value |
    | class="title">blog#16</a> |