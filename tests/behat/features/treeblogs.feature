Feature: Treeblogs plugin standart features BDD
    Test base functionality of LiveStreet Treeblogs plugin standart

@mink:selenium2
    Scenario: Treeblog Blog tests
        Then check is plugin active "treeblogs"
        Given I load fixtures for plugin "treeblogs"

        Given I am on "/login"
        Then I want to login as "admin"

        Then I am on homepage
        Then I should see in element by css "sidebar" values:
        | value |
        | <a class="regular" href="http://livestreet.test/blog/gadgets/">Gadgets</a> |

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

        Then I should see in element by css "sidebar" values:
          | value |
          | test blog1 level2 |


        Then I am on "/blogs/"
        Then element "#blogs-list-original" should contain values:
         | value |
         | test blog1 level1 |
         | test blog1 level2 |

        #reconect blog to other
        Then I am on "/blog/edit/10/"
        Then I select "Gadgets" from "parent_id"
        Then I press element by css "button[name='submit_blog_add']"

        Then I am on "/blogs/"
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

#Then print last response

        Then I fill in "topic_text" with "Changed topic description"

        Then I press element by css "#submit_topic_publish"
        Then I wait "5000"

        Then I should see in element by css "content" values:
          | value |
          | second test topic |
          | class="topic-blog">Gadgets</a> |
          | test2_level1 |

