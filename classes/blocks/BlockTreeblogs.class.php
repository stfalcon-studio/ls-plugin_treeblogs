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

class PluginTreeblogs_BlockTreeblogs extends Block {
    
    public function Exec() {
        /*Дерево целиком*/
        $this->Viewer_Assign('aTree',  $this->Blog_buidlTree() );

        if ($this->GetParam('oBlog')) {
                $iBlogId = $this->GetParam('oBlog')->getId();
        } 
        elseif ($this->GetParam('oTopic')) {
                $iBlogId = $this->GetParam('oTopic')->getBlog()->getId();
        } 

        if (isset($iBlogId)) {
                /*ветка активного блога*/
                $this->Viewer_Assign('aTreePath',  $this->Blog_BuildBranch($iBlogId) );
                /*ативный блог*/
                $this->Viewer_Assign('iTreeBlogId',  $iBlogId );
        } else {
                /*Cтраница без активного блога (нпрм главная, персональный блг). Дерево закрыто*/
                $this->Viewer_Assign('aTreePath',  array() );
                $this->Viewer_Assign('iTreeBlogId',  -1 );
        }
    }
	
}
?>
