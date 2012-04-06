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
 * Класс генерирующий блок "Дерево Категорий"
 *
 */
class PluginTreeblogs_HookMenuTree extends Hook
{

    protected $aActions = array('top', 'index');

    /**
     * Регистрируем нужные хуки
     *
     * @return void
     */
    public function RegisterHook()
    {
        $this->AddHook('topic_show', 'TreeMenuShow', __CLASS__);
        $this->AddHook('blog_collective_show', 'TreeMenuShow', __CLASS__);
        $this->AddHook('blog_show', 'TreeMenuShow', __CLASS__);
        $this->AddHook('personal_show', 'TreeMenuShow', __CLASS__);

        $this->AddHook('init_action', 'InitAction', __CLASS__);
    }

    /**
     * Выводим блок - "дерево категорий".
     * @param array $aData
     *
     * @return void
     */
    public function TreeMenuShow($aData)
    {
        $oBlog = isset($aData['oBlog']) ? $aData['oBlog'] : null;
        $oTopic = isset($aData['oTopic']) ? $aData['oTopic'] : null;
        $this->Viewer_AddBlock(
                'right', 'treeblogs', array('plugin' => 'treeblogs', 'oBlog' => $oBlog, 'oTopic' => $oTopic), Config::Get('plugin.treeblogs.treemenu_block_priority')
        );
    }

    /**
     * Показываем блок "дерево категорий" для index страницы
     *
     * @param array $aVars
     * @return void
     */
    public function InitAction($aVars)
    {
        $action = Router::GetAction();
        if (in_array($action, $this->aActions)) {
            $this->TreeMenuShow(array());
        }
    }

}

