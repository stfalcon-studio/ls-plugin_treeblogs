Feature: Treeblogs plugin standart features BDD
    Test base functionality of LiveStreet Treeblogs plugin standart

@mink:selenium2
    Scenario: Treeblog topic tests (Check success)
        Then check is plugin active "treeblogs"
        Given I load fixtures for plugin "treeblogs"

        Given I am on "/login"
        Then I want to login as "admin"

      #create new topic
        Then I am on "/topic/add"
        Then I select "Gadgets" from "g0_0"
        Then I send js message "change()" to element by css "#g0_0"

        Then I press element by css "#form-topic-add a[onclick='addGroup()']"
        Then I wait "2000"
        Then I select "test1_level1" from "g1_0"
        Then I send js message "change()" to element by css "#g1_0"
        Then I wait "2000"
        Then I select "test1_level2" from "g1_1"
        Then I send js message "change()" to element by css "#g1_1"

        Then I press element by css "#form-topic-add a[onclick='addGroup()']"
        Then I wait "2000"
        Then I select "test2_level1" from "g2_0"
        Then I send js message "change()" to element by css "#g2_0"
        Then I wait "2000"

        Then I fill in "topic_title" with "test topic create"
        Then I fill in "topic_text" with "test descripyion for topic create"
        Then I fill in "topic_tags" with "test topic"
        Then I press element by css "#submit_topic_publish"
        Then I wait "2000"

  Then I should see in element by css "content" values:
          | value |
          | test topic create |
          | >Gadgets</a> |
          | >test1_level2</a> |
          | >test2_level1</a> |

@mink:selenium2
    Scenario: Treeblog edit topic tests
        Then check is plugin active "treeblogs"
        Given I load fixtures for plugin "treeblogs"

        Given I am on "/login"
        Then I want to login as "admin"

      #create new topic
        Then I am on "/topic/edit/5/"

        Then I press element by css "#g1 a[onclick='delGroup(1)']"
        Then I wait "2000"

        Then I press element by css "#form-topic-add a[onclick='addGroup()']"
        Then I wait "2000"

        Then I select "test2_level1" from "g2_0"
        Then I send js message "change()" to element by css "#g2_0"
        Then I wait "2000"

        Then I fill in "topic_text" with "Changed topic description"

        Then I press element by css "#submit_topic_publish"
        Then I wait "2000"

#Then print last response

        Then I should see in element by css "content" values:
          | value |
          | second test topic |
          | class="topic-blog">Gadgets</a> |
          | class="topic-blog">test2_level1</a> |

@mink:selenium2
    Scenario: Check for correct blog tree structure
        Then check is plugin active "treeblogs"
        Given I load fixtures for plugin "treeblogs"

        Given I am on "/login"
        Then I want to login as "admin"

        #create new topic
        Then I am on homepage

        Then element by css "div.menutree" should have structure:
        """
          <ul class="active level0">
            <li class="level0">
              <div class="end"></div>
              <a class="regular" href="http://livestreet.test/blog/gadgets/">Gadgets</a>
            </li>
            <li class="level0">
              <div class="regular" id="d5" onclick="reverseMenu('5')"></div>
              <a class="regular" href="http://livestreet.test/blog/test1_level1/" onclick="reverseMenu('5'); return false;">test1_level1</a>
              <ul class="regular level1" id="m5">
                <li class="level1">
                  <div class="end"></div>
                  <a class="regular" href="http://livestreet.test/blog/test1_level2/">test1_level2</a>
                </li>
              </ul>
            </li>
            <li class="level0">
              <div class="regular" id="d7" onclick="reverseMenu('7')"></div>
              <a class="regular" href="http://livestreet.test/blog/test2_level1/" onclick="reverseMenu('7'); return false;">test2_level1</a>
              <ul class="regular level1" id="m7">
                <li class="level1">
                  <div class="end"></div>
                  <a class="regular" href="http://livestreet.test/blog/test2_level2/">test2_level2</a>
                </li>
              </ul>
            </li>
          </ul>
        """
