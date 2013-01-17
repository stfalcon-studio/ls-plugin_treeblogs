<?php

class PluginTreeblogs_ActionAjax extends PluginTreeblogs_Inherit_ActionAjax
{

    protected function RegisterEvent() {
        parent::RegisterEvent();

        $this->AddEvent('treeblogs', 'EventTreeblogs');
    }

    public function EventTreeblogs() {
        if (!$this->oUserCurrent) {
            $this->Viewer_AssignAjax('noValue', true);
            return;
        }
        $action = getRequest('action');

        switch ($action) {
            case 'newgroup':
                $groupIdx = getRequest('groupIdx');
                $this->Viewer_Assign('groupIdx', $groupIdx);
                $sText = $this->Viewer_Fetch(Plugin::GetTemplatePath(__CLASS__) . 'actions/ActionTopic/empty_group.tpl');
                $this->Viewer_AssignAjax('select', $sText);
                $this->Viewer_AssignAjax('noValue', false);
                return;
            case 'level':
                $sBlogId = getRequest('blogid');
                $nextlevel = getRequest('nextlevel');
                $groupid = getRequest('groupid');

                $this->Viewer_Assign('groupid', $groupid);
                $this->Viewer_Assign('nextlevel', $nextlevel);

                if ($sBlogId == -1) { /* запрос на возврат корня дерева */
                    $aBlogs = $this->Blog_GetMenuBlogs();
                } else { /* Запрос на возврат уровня дерева */
                    $aBlogs = $this->Blog_GetSibling($sBlogId, false);
                }

                if (count($aBlogs)) {
                    reset($aBlogs);
                    $iBlogId = key($aBlogs);
                    $iParentId = $aBlogs[$iBlogId]->getParentId();

                    $this->Viewer_VarAssign();
                    $this->Viewer_Assign('BlogId', $sBlogId);
                    $this->Viewer_Assign('aBlogs', $aBlogs);
                    $this->Viewer_Assign('ParentId', $iParentId);
                    $sText = $this->Viewer_Fetch(Plugin::GetTemplatePath(__CLASS__) . 'actions/ActionTopic/select_blogs.tpl');
                    $this->Viewer_AssignAjax('select', $sText);
                    $this->Viewer_AssignAjax('noValue', false);
                    return;
                }
                $this->Viewer_AssignAjax('noValue', true);
                return;
            case 'children':
                $sBlogId = getRequest('blogid');
                $nextlevel = getRequest('nextlevel');
                $groupid = getRequest('groupid');

                $this->Viewer_Assign('groupid', $groupid);
                $this->Viewer_Assign('nextlevel', $nextlevel);

                $aResult = $this->Blog_GetSubBlogs($sBlogId);
                if ($aResult['count']) {
                    reset($aResult['collection']);
                    $iBlogId = key($aResult['collection']);
                    $iParentId = $aResult['collection'][$iBlogId]->getParentId();

                    $this->Viewer_VarAssign();
                    $this->Viewer_Assign('BlogId', $sBlogId);
                    $this->Viewer_Assign('aBlogs', $aResult['collection']);
                    $this->Viewer_Assign('ParentId', $iParentId);
                    $sText = $this->Viewer_Fetch(Plugin::GetTemplatePath('treeblogs') . 'actions/ActionTopic/select_blogs.tpl');
                    $this->Viewer_AssignAjax('select', $sText);
                    $this->Viewer_AssignAjax('noValue', false);
                    return;
                }
                $this->Viewer_AssignAjax('noValue', true);
                return;
            default:
                $this->Viewer_AssignAjax('noValue', true);
                return;
        }
    }

}

