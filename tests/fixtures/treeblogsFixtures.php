<?php

$sDirRoot = dirname(realpath((dirname(__DIR__)) . "/../../"));
set_include_path(get_include_path().PATH_SEPARATOR.$sDirRoot);

require_once($sDirRoot . "/tests/AbstractFixtures.php");


class treeblogsFixtures extends AbstractFixtures
{
    protected $userId = 1;

    public static function getOrder()
    {
        return 0;
    }

    public function load()
    {
        $mainBlog = $this->getReference('blog-gadgets');

        $firstLevelBlog = $this->createBlog('test1_level1', 'test1_level1', NULL, true);
        $secondLevelBlog = $this->createBlog('test1_level2', 'test1_level2', $firstLevelBlog->getBlogId());
        $firstLevelBlog2 = $this->createBlog('test2_level1', 'test2_level1');
        $secondLevelBlog2 = $this->createBlog('test2_level2', 'test2_level2', $firstLevelBlog2->getBlogId(), true);

        $firstTopic = $this->createTopic($mainBlog->getBlogId(), 'first test topic', NULL, array($mainBlog->getBlogId(), $secondLevelBlog->getBlogId(), $firstLevelBlog2->getBlogId()));
        $SecondTopic = $this->createTopic($mainBlog->getBlogId(), 'second test topic', NULL, array($mainBlog->getBlogId(), $secondLevelBlog->getBlogId()));
    }

    protected function createBlog($blogTitle, $blogUrl, $parentBlogId = NULL, $closedBlog = false)
    {
        $oUserFirst = $this->getReference('user-golfer');

        $oBlogEntity = Engine::GetEntity('Blog');
        $oBlogEntity->setOwnerId($oUserFirst->getId());
        $oBlogEntity->setTitle($blogTitle);
        $oBlogEntity->setDescription('There is some blog description');
        $oBlogEntity->setType('open');
        $oBlogEntity->setDateAdd(date("Y-m-d H:i:s"));
        $oBlogEntity->setUrl($blogUrl);
        $oBlogEntity->setLimitRatingTopic(0);

        $oBlogEntity->setParentId($parentBlogId);
        $oBlogEntity->setBlogsOnly($closedBlog);
        $oBlogEntity->setOrderNum(0);

        $result = $this->oEngine->Blog_AddBlog($oBlogEntity);

        if (!$this->oEngine->Blog_UpdateTreeblogData($result)) {
            throw new Exception("Treeblog is not updated");
        }

        return $result;
    }


    private function createTopic($iBlogId, $sTitle, $type = 'topic', $subblogs = array())
    {
        $this->aActivePlugins = $this->oEngine->Plugin_GetActivePlugins();
        $oUserFirst = $this->getReference('user-golfer');

        $oTopic = Engine::GetEntity('Topic');
        $oTopic->setBlogId($iBlogId);
        $oTopic->setUserId($oUserFirst->getId());
        $oTopic->setUserIp('127.0.0.1');
        $oTopic->setForbidComment(false);
        $oTopic->setType('topic');
        $oTopic->setTitle($sTitle);
        $oTopic->setPublish(true);
        $oTopic->setPublishIndex(true);
        $oTopic->setPublishDraft(true);
        $oTopic->setDateAdd(date("Y-m-d H:i:s"));
        $oTopic->setTextSource('this is test topic description');
        list($sTextShort, $sTextNew, $sTextCut) = $this->oEngine->Text_Cut($oTopic->getTextSource());

        $oTopic->setCutText($sTextCut);
        $oTopic->setText($this->oEngine->Text_Parser($sTextNew));
        $oTopic->setTextShort($this->oEngine->Text_Parser($sTextShort));

        $oTopic->setTextHash(md5($oTopic->getType() . $oTopic->getTextSource() . $oTopic->getTitle()));
        $oTopic->setTags('test topic');
        $oTopic->_setValidateScenario('topic');
        $oTopic->_Validate();

        $oTopic = $this->oEngine->Topic_AddTopic($oTopic);

        $_REQUEST['subblog_id'] = $subblogs;
        $this->oEngine->Topic_MergeTopicBlogs($oTopic->getId(), $oTopic->getBlogId());

        return $oTopic;
    }
}