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

class PluginTreeblogs_ModuleBlog_EntityBlog extends PluginTreeblogs_Inherit_ModuleBlog_EntityBlog
{

	public function getParentId()
	{
		if (isset($this->_aData['parent_id']) && strlen($this->_aData['parent_id'])) {
			return $this->_aData['parent_id'];
		}
		return null;
	}

	public function setParentId($data)
	{
		if ($data == 0) {
			$this->_aData['parent_id'] = null;
		} else {
			$this->_aData['parent_id'] = $data;
		}
	}

}
