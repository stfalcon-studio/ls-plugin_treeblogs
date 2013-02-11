Feature: Treeblogs plugin standart features BDD
    Test base functionality of LiveStreet Treeblogs plugin standart

@mink:selenium2
    Scenario: Treeblog Blog tests
        Then check is plugin active "treeblogs"
        Given I load fixtures for plugin "treeblogs"

        Given I am on "/login"
        Then I want to login as "admin"

        # create blog level1
        Then I am on "/blog/add"
        Then I fill in "blog_title" with "test blog1 level1"
        Then I fill in "blog_url" with "testblog1"
        Then I fill in "blog_description" with "this is test description for blog"


        Then I press element by css "button[name='submit_blog_add']"
        Then I should see in element by css "sidebar" values:
          | value |
          | href="http://livestreet.test/blog/testblog1/">test blog1 level1</a> |

        Then I should see in element by css "content" values:
          | value |
          | test blog1 level1  |

        # create blog level2
        Then I am on "/blog/add"
        Then I select "test blog1 level1" from "parent_id"
        Then I fill in "blog_title" with "test blog1 level2"
        Then I fill in "blog_url" with "testblog2"
        Then I fill in "blog_description" with "this is test description for blog"
        Then I press element by css "button[name='submit_blog_add']"

        Then I am on "/blogs/page2"
        Then element "#blogs-list-original" should contain values:
         | value |
         | test blog1 level1 |
         | test blog1 level2 |

        Then I am on "/blogs/test1_level1"
        Then I should see in element by css "blogs-list-original" values:
          | value |
          | test1_level2 |
          | test1_level1 |

        Then I should not see in element by css "blogs-list-original" values:
          | value |
          | test2_level2 |
          | test2_level1 |
          | Gadgets |

        #reconect blog to other
        Then I am on "/blog/edit/10/"
        Then I select "Gadgets" from "parent_id"
        Then I press element by css "button[name='submit_blog_add']"

        Then I am on "/blogs/page2"
        Then element "#blogs-list-original" should contain values:
          | value |
          | blogs/gadgets/" class="title">Gadgets</a> |
          | test blog1 level2 |

        Then I am on "/blogs/gadgets/"
        Then element "#blogs-list-original" should contain values:
          | value |
          | Gadgets |
          | test blog1 level2 |

  @mink:selenium2
  Scenario: Treeblog topic tests (Check fail)
    Then check is plugin active "treeblogs"
    Given I load fixtures for plugin "treeblogs"

    Given I am on "/login"
    Then I want to login as "admin"

  #create new topic (mast fail by duplicating of empty groups)
    Then I am on "/topic/add"
    Then I select "Gadgets" from "g0_0"
    Then I send js message "change()" to element by css "#g0_0"

    Then I press element by css "#form-topic-add a[onclick='addGroup()']"
    Then I wait "2000"

    Then I press element by css "#form-topic-add a[onclick='addGroup()']"
    Then I wait "2000"

    Then I fill in "topic_title" with "test topic1"
    Then I fill in "topic_text" with "test descripyion for topic"
    Then I fill in "topic_tags" with "test topic"
    Then I press element by css "#submit_topic_publish"

    Then I should not see in element by css "groups" values:
      | value |
      | <div id="g1" class="group"> |
      | <div id="g2" class="group"> |

    Then I should see in element by css "content" values:
      | value |
      | Subblog is not selected |

@mink:selenium2
    Scenario: Treeblog topic tests (Check fail)
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
        Then I select "test2_level1" from "g1_0"
        Then I send js message "change()" to element by css "#g1_0"

        Then I press element by css "#form-topic-add a[onclick='addGroup()']"
        Then I wait "2000"
        Then I select "Gadgets" from "g2_0"
        Then I send js message "change()" to element by css "#g2_0"

        Then I press element by css "#form-topic-add a[onclick='addGroup()']"
        Then I wait "2000"
        Then I select "test1_level1*" from "g3_0"
        Then I send js message "change()" to element by css "#g3_0"
        Then I wait "2000"

        Then I fill in "topic_title" with "test topic1"
        Then I fill in "topic_text" with "test descripyion for topic"
        Then I fill in "topic_tags" with "test topic"
        Then I press element by css "#submit_topic_publish"

        Then I should see in element by css "content" values:
          | value |
          | Trying of connect to banned blog |
          | Connected blogs are duplicated |

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
          | class="topic-blog">Gadgets</a> |
          | class="topic-blog">test1_level2</a> |
          | class="topic-blog">test2_level1</a> |

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
