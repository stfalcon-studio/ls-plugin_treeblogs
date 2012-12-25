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
 * Плагин Treeblogs. Хуки для блогов
 */
class PluginTreeblogs_HookBlog extends Hook
{

    /**
     * Регистрируем нужные хуки
     *
     * @return void
     */
    public function RegisterHook()
    {
        $this->AddHook('template_form_add_blog_begin', 'TemplateFormAddBlogBegin', __CLASS__, -100);
        $this->AddHook('template_topics_list_begin', 'TemplateTopicListBegin', __CLASS__, -100);

        $this->AddHook('blog_add_show', 'BlogAddShow', __CLASS__);
        $this->AddHook('blog_edit_show', 'BlogEditShow', __CLASS__);

        $this->AddHook('blog_add_after', 'BlogToBlog', __CLASS__);
        $this->AddHook('blog_edit_after', 'BlogToBlog', __CLASS__);

        $this->AddHook('blog_collective_show', 'BlogShow', __CLASS__, -100);
    }

    /**
     * Шаблон с списком блогов. Цепляется на хук в начало формы создания/редактирования блога
     *
     * @return string
     */
    public function TemplateFormAddBlogBegin()
    {
        $iBlogId = getRequest('blog_id');
        $data = array();
        if ($iBlogId != NULL) {
            $oBlog = $this->Blog_GetBlogById($iBlogId);
            if (!isPost('submit_blog_add')) {
             $data =  array(
                'order_num' => $oBlog->getOrderNum(),
                'parent_id' => $oBlog->getParentId(),
                'blogs_only' => $oBlog->getBlogsOnly()
             );
            }
        } else {
            $data =  array(
                'order_num' => 0
             );
            if (getRequest('parent_id')) {
                $data['parent_id'] = getRequest('parent_id');
            }
        }
        $aBlogs = $this->Blog_GetBlogsForSelect($iBlogId);
        $this->Viewer_Assign('data', $data);
        $this->Viewer_Assign('aBlogs', $aBlogs);
        
        return $this->Viewer_Fetch(Plugin::GetTemplatePath(__CLASS__) . 'actions/ActionBlog/form_add_blog_to_blog.tpl');
    }

    /**
     * Хук на список топиков, для отображения фильтра блогов
     *
     * @return string
     */
    public function TemplateTopicListBegin()
    {
        return $this->Viewer_Fetch(Plugin::GetTemplatePath(__CLASS__) . 'filter.subblogs.tpl');
    }

    /**
     * Хук екшена добавления блога
     */
    public function BlogAddShow()
    {
        if (!getRequest('order_num')) {
            $_REQUEST['order_num'] = 0;
        }
        $aBlogs = $this->Blog_GetBlogsForSelect();
        $this->Viewer_Assign('aBlogs', $aBlogs);
    }

    /**
     * Хук кшена редактирования блога
     *
     * @param array $aData
     */
    public function BlogEditShow($aData)
    {
        $oBlog = $aData['oBlog'];
        if (!isPost('submit_blog_add')) {
            $_REQUEST['order_num'] = $oBlog->getOrderNum();
            $_REQUEST['parent_id'] = $oBlog->getParentId();
            $_REQUEST['blogs_only'] = $oBlog->getBlogsOnly();
        }
        $aBlogs = $this->Blog_GetBlogsForSelect($oBlog->getId());
        $this->Viewer_Assign('aBlogs', $aBlogs);
    }

    /**
     * Обновление данных treeblogs при сохранении
     *
     * @param array $aData
     */
    public function BlogToBlog($aData)
    {
        $oBlog = $aData['oBlog'];
        $oBlog->setParentId(getRequest('parent_id'));
        $oUser = $this->User_GetUserCurrent();
        if ($oUser->isAdministrator()) {
            $oBlog->setOrderNum(getRequest('order_num', 0));
            $oBlog->setBlogsOnly(getRequest('blogs_only', false));
        }
        $this->Blog_UpdateTreeblogData($oBlog);
    }

    /**
     * Добавляем подблоги
     *
     * @param array $aData
     */
    public function BlogShow($aData)
    {
        $oBlog = $aData['oBlog'];
        $oUserCurrent = $this->User_GetUserCurrent();
        if ($oBlog->getBlogsOnly() && !($oUserCurrent && $oUserCurrent->isAdministrator())) {
            return Router::Location('error');
        }
        $sShowType = $aData['sShowType'];
        $aResult = $this->Blog_GetSubBlogs($oBlog->getId());
        $this->Viewer_Assign('aBlogsSub', $aResult['collection']);
        $this->makePaging($oBlog, $sShowType);
        $aBlogFilter = explode(' ', getRequest('b'));
        $this->Viewer_Assign('aBlogFilter', $aBlogFilter);
    }

    /**
     *  Формируем пагинацию
     * @param oBlog
     * @param string sShowType
     */
    protected function makePaging($oBlog, $sShowType)
    {
        $urlParams = '';
        $getParams = array();
        getRequest('b') ? $getParams['b'] = getRequest('b') : true;
        $iPage = $this->GetPage(($sShowType == 'good') ? 0 : 1, 2) ? $this->GetPage(($sShowType == 'good') ? 0 : 1, 2) : 1;
        $aResult = $this->Topic_GetTopicsByBlog($oBlog, $iPage, Config::Get('module.topic.per_page'), $sShowType);
        $aPaging = ($sShowType == 'good') ? $this->Viewer_MakePaging($aResult['count'], $iPage, Config::Get('module.topic.per_page'), 4, rtrim($oBlog->getUrlFull() . $urlParams, '/'), $getParams) : $this->Viewer_MakePaging($aResult['count'], $iPage, Config::Get('module.topic.per_page'), 4, $oBlog->getUrlFull() . $sShowType . $urlParams, $getParams);
        $this->Viewer_Assign('aPaging', $aPaging);
    }

    /**
     * Получаем страницу
     *
     * @param int $iParamNum
     * @param int $iItem
     * @return int|null
     */
    protected function GetPage($iParamNum, $iItem = null)
    {
        $params = Router::GetParams();
        if (!isset($params[$iParamNum])) {
            return null;
        }
        if (!is_null($iItem)) {
            preg_match('/^(page(\d+))?$/i', $params[$iParamNum], $matches);
            if (isset($matches[$iItem])) {
                return $matches[$iItem];
            } else {
                return null;
            }
        } else {
            if (isset($params[$iParamNum])) {
                return $params[$iParamNum];
            } else {
                return null;
            }
        }
    }
}
