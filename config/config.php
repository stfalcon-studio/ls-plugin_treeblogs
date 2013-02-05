<?php
/* ---------------------------------------------------------------------------
 * @Plugin Name: Treeblogs
 * @Plugin Id: Treeblogs
 * @Plugin URI:
 * @Description: Дерево блогов
 * @Author: mackovey@gmail.com
 * @Author URI: http://stfalcon.com
 * @LiveStreet Version: 0.5
 * @License: GNU GPL v2, http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * ----------------------------------------------------------------------------
 */

$config = array();
$config['blogs']['count'] = 0;

/**
 * Приоритет блока "дерево каталогов". 0-disabled
 */
$config['treemenu_block_priority'] = 110;


// Используется для тестов пагинации
if (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST']=='livestreet.test') {
    Config::Set('module.blog.per_page', 5);
}

/**
 * Регистрация таблицы topic_blog
 */
Config::Set('db.table.topic_blog', '___db.table.prefix___topic_blog');

return $config;
