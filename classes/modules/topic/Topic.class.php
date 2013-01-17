<?php

/* ---------------------------------------------------------------------------
 * @Plugin Name: Treeblogs
 * @Plugin Id: Treeblogs
 * @Plugin URI:
 * @Description: Дерево блогов
 * @Author: mackovey@gmail.com
 * @Author URI: http://stfalcon.com
 * @LiveStreet Version: 0.4.2
 * @License: GNU GPL v2, http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * ----------------------------------------------------------------------------
 */

class PluginTreeblogs_ModuleTopic extends PluginTreeblogs_Inherit_ModuleTopic
{

    /**
     * Список топиков по модифицированному фильтру
     *
     * @param  array $aFilter
     * @param  int   $iPage
     * @param  int   $iPerPage
     * @return array
     */
    public function GetTopicsByFilter($aFilter, $iPage = 0, $iPerPage = 0, $aAllowData = array('user' => array(), 'blog' => array('owner' => array()),
        'vote', 'favourite', 'comment_new')
    )
    {
        return parent::GetTopicsByFilter($this->_getModifiedFilterSubBlogs($aFilter), $iPage, $iPerPage, $aAllowData
        );
    }

    /**
     * Количество топиков по фильтру
     *
     * @param array $aFilter
     * @return integer
     */
    public function GetCountTopicsByFilter($aFilter)
    {
        return parent::GetCountTopicsByFilter($this->_getModifiedFilterSubBlogs($aFilter));
    }

    /**
     * Фильтр с дополнительными параметрами выборки для дерева блогов
     *
     * @param array $aFilter
     * @return array
     */
    protected function _getModifiedFilterSubBlogs(array $aFilter)
    {
        $subBlogsFilter = getRequest('b');
        $subBlogs = array();
        if ($subBlogsFilter) {
            if ($aSubBlogsUrl = explode(' ', $subBlogsFilter)) {
                foreach ($aSubBlogsUrl as $subBlogUrl) {
                    if ($oBlog = $this->Blog_GetBlogByUrl($subBlogUrl)) {
                        array_push($subBlogs, $oBlog->getId());
                        $subBlogsMore = $this->_getSubBlogs($oBlog->getId());
                        foreach ($subBlogsMore as $blogMore) {
                            array_push($subBlogs, $blogMore);
                        }
                    }
                }
                $aFilter['blog_id'] = $subBlogs;
            }
        } elseif (isset($aFilter['blog_id'])) {
            if (!is_array($aFilter['blog_id'])) {
                $aFilter['blog_id'] = array($aFilter['blog_id']);
            }
            $aBlogsId = $aFilter['blog_id'];
            foreach ($aFilter['blog_id'] as $sBlogId) {
                $subBlogs = $this->_getSubBlogs($sBlogId);
                $aBlogsId = array_merge($aBlogsId, $subBlogs);
            }
            $aFilter['blog_id'] = $aBlogsId;
        }

        if (isset($aFilter['blog_type'])) {
            if (in_array('company', $aFilter['blog_type'])) {
                $aFilter['blog_type'][] = 'open';
            }
        }

        return $aFilter;
    }

    /**
     * Получаем под блоги
     *
     * @param int $iBlogId
     * @return array
     */
    protected function _getSubBlogs($iBlogId)
    {
        $aBlogId = array();
        $aSubBlogId = $this->Blog_GetSubBlogs($iBlogId, 0, 0, true);
        foreach ($aSubBlogId['collection'] as $iSubBlogId) {
            $aSubBlogIds = $this->_getSubBlogs($iSubBlogId);
            $aBlogId = array_merge($aBlogId, $aSubBlogIds);
        }
        return $aBlogId;
    }

    /**
     * Вспомогательная функция для MergeTopicBlogs
     * Мержим два дерева - вычесляем положение в семье - "лист" или "дядь, дедушек, родителей,..."
     *
     * @param array, array
     * @return array
     */
    private function calcInFamilyQuality($to, $from)
    {
        $i = count($from) - 1;
        foreach ($from as $node) {
            if (!isset($to[$node])) {
                $to[$node] = $i;
            } else {
                if ($to[$node] == 0 and $i > 0) {
                    $to[$node] = $i;
                }
            }
            $i--;
        }
        return $to;
    }

    /**
     * Мержим топиковые блоги, Добавление-удаление в базе данных.
     * Исключаем блоги являющиеся одновременно и листами
     * и "дядями, дедушками, родителями, прадедушками, ....".
     * Только листы!!!
     * $BlogId - дефолтный блог
     * POST[subblog_id] - список второстепенных блогов. Функция работает в режиме post запроса
     *
     * @param int $TopicId
     * @param int $BlogId
     * @return array
     */
    public function MergeTopicBlogs($TopicId, $BlogId)
    {
        /* Блоги из запроса */
        $blogs_post = getRequest('subblog_id', array());
        /* Блоги из базы */
        $blogs_db = $this->oMapperTopic->GetTopicBlogs($TopicId);

        /* Массив несущий положение в семье. 0 - только лист, 1 и родитель и возможно лист */
        $aFamilQuality = array();

        /* Дефолтная ветка, исключаем сразу все блоги из это ветки */
        $aTreeDefBlog = $this->Blog_BuildBranch($BlogId);
        foreach ($aTreeDefBlog as $blog_id) {
            $aFamilQuality[$blog_id] = 1;
        }

        /* Второстепенные ветки из пост запроса */
        foreach ($blogs_post as $blog_id) {
            if ($blog_id > 0) {
                /* вычесляем верхних родственников */
                $aFamilQuality = $this->calcInFamilyQuality(
                        $aFamilQuality, $this->Blog_BuildBranch($blog_id)
                );
            }
        }

        /* листы */
        $aLeaf = array();

        /* исключаем всех кроме листов */
        foreach ($aFamilQuality as $blog_id => $cnt) {
            /* если 0 - лист, >0 верхний родственник */
            if ($cnt == 0) {
                array_push($aLeaf, $blog_id);
            }
        }

        /* Удаляем из базы данных если
         * 1. блог выключили на форме
         * 2. блог стал родителем (потерял статус исключительно листа)
         */
        foreach ($blogs_db as $blog) {
            if (!in_array($blog, $aLeaf)) {
                $this->oMapperTopic->DeleteTopicFromSubBlog($blog, $TopicId);
            }
        }
        /* Вставляем в базу данных
         * 1. Блог-Лист отсутствует в базе данных
         * */
        foreach ($aLeaf as $blog_id) {
            if (!in_array($blog_id, $blogs_db)) {
                $this->oMapperTopic->AddTopicToSubBlog($blog_id, $TopicId);
            }
        }
        $this->Cache_Delete("topic_branches_" . $TopicId);
    }

    /**
     * Возвращаем второстипенные блоги топика
     *
     * @param int $TopicId
     * @param string $sOrder edit|show
     * @return array
     */
    public function GetTopicBlogs($TopicId)
    {
        return $this->oMapperTopic->GetTopicBlogs($TopicId);
    }

    /**
     * Строим все доступные ветки для топика
     * @param oTopic
     * @return int aBlogId
     * */
    public function GetTopicBranches($oTopic)
    {
        if (false === ($oBlogsTree = $this->Cache_Get("topic_branches_" . $oTopic->getId()))) {
            $oBlogsTree = array();
            $aSubBlogs = $this->Topic_GetTopicBlogs($oTopic->getId());

            foreach ($aSubBlogs as $subblogid) {
                $subBlog = $this->Blog_BuildBranch($subblogid);
                array_push($oBlogsTree, $this->Blog_GetBlogsAdditionalData($subBlog));
            }
            $this->Cache_Set($oBlogsTree, "topic_branches_" . $oTopic->getId(), array('treeblogs.branches'), 60 * 60 * 3);
        }
        return $oBlogsTree;
    }

}
