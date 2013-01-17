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

/**
 * Модуль Blog плагина Treeblogs
 */
class PluginTreeblogs_ModuleBlog extends PluginTreeblogs_Inherit_ModuleBlog
{

    /**
     * Возвращаем блоги "родные братья" по дереву.
     * BlogId - id одного из братьев
     *
     * @param int BlogId
     * @return aBlogId|int
     * */
    public function GetSibling($iBlogId, $bReturnIdOnly = true)
    {
        $oBlog = $this->GetBlogById($iBlogId);
        if (!$oBlog) {
            return array();
        }
        $iParentId = $oBlog->getParentId();
        if (!is_null($iParentId)) {
            $aBlogId = $this->oMapperBlog->GetAllSubBlogs($iParentId);
        } else {
            $aBlogId = $this->oMapperBlog->GetMenuBlogs($this->oUserCurrent ? $this->oUserCurrent->getId() : 0);
        }
        if ($bReturnIdOnly) {
            return $aBlogId;
        }

        return $this->GetBlogsAdditionalData($aBlogId);
    }

    /**
     * Строим ветку для блога
     *
     * @param int BlogId
     * @return array aBlogId
     * */
    public function BuildBranch($BlogId)
    {
        $sLang = Config::Get('lang_current');
        if (false === ($res = $this->Cache_Get("blogs_tree_{$BlogId}_{$sLang}"))) {
            $res = array();
            array_unshift($res, $BlogId);
            $workid = $BlogId;
            while (true) {
                $workid = $this->oMapperBlog->getParentBlogId($workid);
                if (!isset($workid)) {
                    break;
                } else {
                    array_unshift($res, $workid);
                }
            }
            $this->Cache_Set($res, "blogs_tree_{$BlogId}_{$sLang}", array(), 60 * 60 * 3);
        }
        return $res;
    }

    /**
     * Строим дерево.
     * При $iParentId = null строим полное дерево.
     * Функция рекурсивна
     *
     * @param int ParentId
     * @return array
     * */
    public function buidlTree($iParentId = null, $bPlain = false)
    {
        $aTree = array();
        $sLang = Config::Get('lang_current');
        if ($iParentId == null) {
            // Стартовая позиция, нулевой уровень, родителей нет. Пытаемся найти дерево в кеше
            if (false === ($aTree = $this->Cache_Get("blogs_full_tree_{$sLang}"))) {
                $aBlogsId = $this->oMapperBlog->GetMenuBlogs(0);
                $aoBlogs = $this->Blog_GetBlogsAdditionalData($aBlogsId);
                foreach ($aoBlogs as $oBlog) {
                    $aTree[$oBlog->getId()]['blog'] = $oBlog;
                    $child = $this->buidlTree($oBlog->getId());
                    $bOpen = false;
                    foreach ($child as $branch) {
                        if (count($branch['child'])) {
                            $bOpen = true;
                        }
                    }
                    $aTree[$oBlog->getId()]['child'] = $this->buidlTree($oBlog->getId());
                    $aTree[$oBlog->getId()]['open'] = $bOpen;
                }
                $this->Cache_Set($aTree, "blogs_full_tree_{$sLang}", array('blog_tree'), 60 * 60 * 3);
            }
        } else {
            // Уровни имеющие родителя, уровень >= 1
            $aBlogs = $this->GetSubBlogs($iParentId, 1, Config::Get('plugin.treeblogs.blogs.count'));
            foreach ($aBlogs['collection'] as $oBlog) {
                $aTree[$oBlog->getId()]['blog'] = $oBlog;
                $child = $this->buidlTree($oBlog->getId());
                $bOpen = false;
                foreach ($child as $branch) {
                    if (count($branch['child'])) {
                        $bOpen = true;
                    }
                }
                $aTree[$oBlog->getId()]['child'] = $this->buidlTree($oBlog->getId());
                $aTree[$oBlog->getId()]['open'] = $bOpen;
            }
        }

        if ($bPlain) {
            $aBlogs = $this->buildFlat($aTree);
            return $aBlogs;
        }
        return $aTree;
    }

    protected function buildFlat($aTree)
    {
        $aBlogs = array();
        foreach ($aTree as $key => $aBranch) {
            $aBlogs[$key] = $aBranch['blog'];
            $aChildBlog = $this->buildFlat($aBranch['child']);
            $aBlogs += $aChildBlog;

        }
        return $aBlogs;
    }

    /**
     * Обновление данные блога по treeblog
     *
     * @param ModuleBlog_EntityBlog $oBlog
     * @return boolean
     */
    public function UpdateTreeblogData($oBlog)
    {
        $this->Cache_Delete('blogs_parent_relations');
        // чистим кеш полного дерева
        $this->Cache_Delete('blogs_full_tree');
        // подчищаем все деревья для топиков
        $this->Cache_Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array('blog_tree'));
        $this->Cache_Clean(Zend_Cache::CLEANING_MODE_ALL, array('treeblogs.branches'));
        $aBlogsId = $this->oMapperBlog->GetMenuBlogs();
        foreach ($aBlogsId as $blogId) {
            /* чистим кеш веток блогов */
            $this->Cache_Delete('blogs_tree_' . $blogId);
        }
        return $this->oMapperBlog->UpdateTreeblogData($oBlog);
    }

    /**
     * Получаем под-блоги
     *
     * @param int $iBlogId
     * @return array
     */
    public function GetSubBlogs($iBlogId, $iPage = 0, $iPerPage = 0, $bReturnIdOnly = false)
    {
        $sKey = "blog_subblogs_" . $iBlogId . "_{$iPage}_{$iPerPage}";
        if (false === ($data = $this->Cache_Get($sKey))) {
            $data = ($iPage * $iPerPage != 0) ? array(
                'collection' => $this->oMapperBlog->GetSubBlogs($iBlogId, $iCount, $iPage, $iPerPage),
                'count' => $iCount
                    ) : array(
                'collection' => $this->oMapperBlog->GetAllSubBlogs($iBlogId),
                'count' => $this->oMapperBlog->GetCountSubBlogs($iBlogId)
                    );

            $this->Cache_Set($data, $sKey, array("blog_update", "blog_new"), 60 * 60 * 24 * 2);
        }

        if ($bReturnIdOnly) {
            return $data;
        }

        $data['collection'] = $this->GetBlogsAdditionalData($data['collection']);
        return $data;
    }

    /**
     * Возвращает блоги для меню
     *
     * @param boolean $bReturnIdOnly
     * @param boolean $bShowPersonal
     * @return array
     */
    public function GetMenuBlogs($bReturnIdOnly = false, $bShowPersonal = false)
    {
        $data = array();
        if ($bShowPersonal && $this->oUserCurrent) {
            $data = $this->oMapperBlog->GetMenuBlogs($this->oUserCurrent->getId());
        } else {
            $data = $this->oMapperBlog->GetMenuBlogs(0);
        }

        // Возвращаем только иденитификаторы
        if ($bReturnIdOnly) {
            return $data;
        }
        return $this->GetBlogsAdditionalData($data);
    }

    /**
     * Получаем блоги для выбора, исключая определенный блог
     *
     * @param int $iBlogId
     * @return array
     */
    public function GetBlogsForSelect($iBlogId = null)
    {
        $aBlogSelect = array();
        $aBlogs = $this->oMapperBlog->GetBlogsForSelect($iBlogId);

        foreach ($aBlogs as $oBlog) {
            if (is_null($oBlog->getParentId())) {
                array_push($aBlogSelect, $oBlog);
                $aSubBlogs = $this->_getSubBlogs($oBlog->getId(), $aBlogs, 1);
                if (count($aSubBlogs)) {
                    foreach ($aSubBlogs as $oSubBlog) {
                        array_push($aBlogSelect, $oSubBlog);
                    }
                }
            }
        }
        return $aBlogSelect;
    }

    /**
     * Получаем подблоги
     *
     * @param int blogId
     * @param array aBlogs
     * @param int level
     * @return array
     */
    protected function _getSubBlogs($iBlogId, $aBlogs, $level)
    {
        $aBlogsSub = array();
        foreach ($aBlogs as $oBlog) {
            if ($oBlog->getParentId() == $iBlogId) {
                $oBlog->setTitle(str_repeat('&nbsp;-&nbsp;', $level) . $oBlog->getTitle());
                array_push($aBlogsSub, $oBlog);
                $aSubBlogs = $this->_getSubBlogs($oBlog->getId(), $aBlogs, $level + 1);
                if (count($aSubBlogs)) {
                    foreach ($aSubBlogs as $oSubBlog) {
                        array_push($aBlogsSub, $oSubBlog);
                    }
                }
            }
        }
        return $aBlogsSub;
    }

    /**
     * Получаем главного родителя
     * @param int $blogId
     * @return array
     */
    public function GetTopParentId($blogId)
    {
        if (false === ($aBlogs = $this->Cache_Get("blogs_parent_relations"))) {
            $aBlogs = $this->oMapperBlog->GetBlogRelations();
            $this->Cache_Set($aBlogs, "blogs_parent_relations", array(), 60 * 60 * 3);
        }
        return $this->_getTopId($aBlogs, $blogId);
    }

    /**
     * Получаем родителя блога
     *
     * @param array $aBlogs
     * @param int $blogId
     * @return int
     */
    protected function _getTopId($aBlogs, $blogId)
    {
        if (!array_key_exists($blogId, $aBlogs)) {
            return null;
        }
        if (is_null($aBlogs[$blogId])) {
            return $blogId;
        } else {
            return $this->_getTopId($aBlogs, $aBlogs[$blogId]);
        }
    }

    public function GetBlogOnlyBlogs()
    {
        return $this->oMapperBlog->GetBlogOnlyBlogs();
    }

}