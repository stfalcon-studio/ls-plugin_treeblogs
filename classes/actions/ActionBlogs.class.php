<?php

/**
 *
 */
class PluginTreeblogs_ActionBlogs extends PluginTreeblogs_Inherit_ActionBlogs
{

    protected function RegisterEvent()
    {
        parent::RegisterEvent();
        $this->AddEventPreg('/^[\w\-\_]+$/i', '/^(page(\d+))?$/i', 'EventShowBlogsTree');
    }

    public function EventShowBlogsTree()
    {
        /**
		 * По какому полю сортировать
		 */
		$sOrder='blog_rating';
		if (getRequest('order')) {
			$sOrder=getRequest('order');
		}
		/**
		 * В каком направлении сортировать
		 */
		$sOrderWay='desc';
		if (getRequest('order_way')) {
			$sOrderWay=getRequest('order_way');
		}

        $sBlogUrl = $this->sCurrentEvent;

        if (!($oBlog = $this->Blog_GetBlogByUrl($sBlogUrl))) {
            return parent::EventNotFound();
        }

//        $iPage = $this->GetParamEventMatch(0, 2) ? $this->GetParamEventMatch(0, 2) : 1;
        $aBlogs = $this->Blog_buidlTree($oBlog->getId(), true);

//        $aResult = $this->Blog_GetSubBlogs($oBlog->getId(), $iPage, Config::Get('module.blog.per_page'));
//        $aBlogs = $aResult['collection'];

        /**
         * Формируем постраничность
         */
//        $aPaging = $this->Viewer_MakePaging($aResult['count'], $iPage, Config::Get('module.blog.per_page'), 4, Router::GetPath('blogs'));
        /**
         * Загружаем переменные в шаблон
         */
//        $this->Viewer_Assign('aPaging', $aPaging);
        $this->Viewer_Assign("aBlogs", $aBlogs);
        $this->Viewer_Assign("sBlogOrder", htmlspecialchars($sOrder));
        $this->Viewer_Assign("sBlogOrderWay", htmlspecialchars($sOrderWay));
        $this->Viewer_Assign("sBlogOrderWayNext", htmlspecialchars($sOrderWay == 'desc' ? 'asc' : 'desc'));
        $this->Viewer_AddHtmlTitle($this->Lang_Get('blog_menu_all_list'));
        /**
         * Устанавливаем шаблон вывода
         */
        $this->SetTemplateAction('index');

        $this->Viewer_AddBlock('right', 'blogslist', array('plugin' => 'treeblogs', 'oBlog' => $oBlog), Config::Get('plugin.treeblogs.treemenu_block_priority'));
    }

    protected function EventShowBlogs() {
        $this->Viewer_AddBlock('right', 'blogslist', array('plugin' => 'treeblogs'), Config::Get('plugin.treeblogs.treemenu_block_priority'));
        parent::EventShowBlogs();
    }
}