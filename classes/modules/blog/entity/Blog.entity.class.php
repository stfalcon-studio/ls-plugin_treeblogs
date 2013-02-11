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
        return $this->_aData['parent_id'];
    }

    public function getOrderNum()
    {
        return $this->_aData['order_num'];
    }

    public function getBlogsOnly()
    {
        return $this->_aData['blogs_only'];
    }

    public function setParentId($data)
    {
        if ($data == 0) {
            $this->_aData['parent_id'] = null;
        } else {
            $this->_aData['parent_id'] = $data;
        }
    }

    public function getParentBlog()
    {
        if (!isset($this->_aData['parent_blog'])) {
            if ($this->_aData['parent_id']) {
                $this->_aData['parent_blog'] = $this->Blog_GetBlogById($this->_aData['parent_id']);
            } else {
                $this->_aData['parent_blog'] = null;
            }
        }

        return $this->_aData['parent_blog'];
    }

    public function setOrderNum($data)
    {
        $this->_aData['order_num'] = $data;
    }

    public function setBlogsOnly($data)
    {
        $this->_aData['blogs_only'] = (bool) $data;
    }

    public function getBlogsUrl()
    {
        return Router::GetPath('blogs').$this->getUrl().'/';
    }
}
