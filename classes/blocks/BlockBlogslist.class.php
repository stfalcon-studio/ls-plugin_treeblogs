<?php

class PluginTreeblogs_BlockBlogslist extends Block
{

    public function Exec()
    {
        /* Дерево целиком */
        $this->Viewer_Assign('aTree', $this->Blog_buidlTree());

        if ($oBlog = $this->GetParam('oBlog')) {
            $iBlogId = $oBlog->getId();
            /* ветка активного блога */
            $aTreePath = $this->Blog_BuildBranch($iBlogId);
            array_pop($aTreePath);
            $this->Viewer_Assign('aTreePath', $aTreePath);
            /* ативный блог */
            $this->Viewer_Assign('iTreeBlogId', $iBlogId);
        } else {
            /* Cтраница без активного блога (нпрм главная, персональный блг). Дерево закрыто */
            $this->Viewer_Assign('aTreePath', array());
            $this->Viewer_Assign('iTreeBlogId', -1);
        }
    }

}

?>
