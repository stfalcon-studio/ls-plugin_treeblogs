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
 * Класс дополняющий работу с топиками новым функционалом
 * Добавление, редактирование, вывод топика/хлебные крохи топиковых блог-ов
 */
class PluginTreeblogs_HookTopic extends Hook
{

    /**
     * Регистрируем нужные хуки
     *
     * @return void
     */
    public function RegisterHook()
    {
        /* Шаблонные хуки для редактировани/добавления и отображения топика */

        /* template_form_add_topic_topic_begin - дополняет в шаблоне форму доб/ред
         * элементами <select> всех блогов топика.
         * Включен в actions/ActionTopic/add.tpl
         */
        $this->AddHook('template_form_add_topic_topic_begin', 'TemplateFormAddTopicBegin', __CLASS__);
        /* template_get_topics_blogs - дополняет отображение топика
         * "хлебными крошками" блогов связанных с ним.
         * Влияет на topic.tpl, topic_list.tpl
         */
        $this->AddHook('template_get_topics_blogs', 'TemplateTopicShow', __CLASS__);

        /* Акшин хук для редактировани/добавления
         * Хук цепляеться на обработку запроса ред/доб топика
         * Обновляет базу данных.
         */
        $this->AddHook('topic_add_after', 'TopicSubmitAfter', __CLASS__);
        $this->AddHook('topic_edit_after', 'TopicSubmitAfter', __CLASS__);

        $this->AddHook('check_topic_fields', 'CheckFields', __CLASS__);
    }

    /**
     * Формируем данные для отображеня группы блогов при редактировании/добавлении топика.
     * Используеться в шаблоне в качестве генирации <select>.
     *
     * @return string
     */
    public function TemplateFormAddTopicBegin()
    {
        $iTopicId = getRequest('topic_id');
        $iBlogId = getRequest('blog_id');
        $aSubBlogs = getRequest('subblog_id') ? getRequest('subblog_id') : array();
//        var_dump($iBlogId);
//        var_dump($aSubBlogs);
        /* массив групп */
        $aGroups = array();
        $oTopic = $this->Topic_GetTopicById($iTopicId);

        if ($oTopic) {
            /* Редактирование топика */
            $aoTopic = $this->Topic_GetTopicsAdditionalData($iTopicId);
            $oTopic = $aoTopic[$iTopicId];

            /* дополнительные блоги-листы топика */
            $aSubBlogs = $this->Topic_GetTopicBlogs($oTopic->getId());
            /* основной блог топика. Помещаем в верх */
            //array_unshift($aSubBlogs, $oTopic->getBlogId());
        } else {
            /* Добавлении нового топика */
            if (!is_null($iBlogId)) { /* добавление с ошибкой */
                array_push($aSubBlogs, $iBlogId);
            } else { /* первый заход, выводим корень */
                /* 0 уровень дерева блогов, первый элемент блог по умолчанию */
                $aRootBlogs = $this->Blog_GetMenuBlogs(false, true);
                reset($aRootBlogs);
                $iBlogId = key($aRootBlogs);
                /* второй уровень дерева (если он есть у $iBlogId) */
                $aResult = $this->Blog_GetSubBlogs($iBlogId);

                array_push($aGroups, array(
                    'iBlogId' => $iBlogId,
                    'aoLevelBlogs' => array($aRootBlogs, $aResult['collection']),
                    'aiLevelSelectedBlogId' => array($iBlogId),
                        )
                );
            }
        }
        /* Формируем массив групп с полным перечислением родственных блогов и уровней
         * Одна итерация - одна группа.
         * Для каждого блога-листа создаёться своя группа блогов руководствуясь деревом блогов.
         * */
        foreach ($aSubBlogs as $iBlogId) {
            /* Массив массивов. Блоги всех уровней. Для заполнения <select> */
            $aoLevelBlogs = array();

            /* Активные блоги в ветке. Для selected="selecеted" */
            $aiLevelSelectedBlogId = $this->Blog_BuildBranch($iBlogId);

            /* Формируем уровни блогов для <select> имеющие связи с топиком */
            foreach ($aiLevelSelectedBlogId as $iBlogId) {
                array_push($aoLevelBlogs, $this->Blog_GetSibling($iBlogId, false));
            }

            /* Ищем дочерние блоги для последнего в цепочке блога.
             * Топик может не иметь с нем никакой связи.
             * Отображаеться как невыбраный <select>
             */
            $aResult = $this->Blog_GetSubBlogs($iBlogId);
            if ($aResult['count']) {
                array_push($aoLevelBlogs, $aResult['collection']);
            }

            array_push($aGroups, array('iBlogId' => $iBlogId,
                'aoLevelBlogs' => $aoLevelBlogs,
                'aiLevelSelectedBlogId' => $aiLevelSelectedBlogId,
                    )
            );
        }

        $this->Viewer_Assign('aGroup', $aGroups);
        return $this->Viewer_Fetch(Plugin::GetTemplatePath('treeblogs') . 'actions/ActionTopic/form_edit_topic.tpl');
    }

    /**
     * Хук цепляющийся на пост обработку ред/доб топика.
     * Создаём связи между топиком и блогами. Работа с базой данных
     *
     * @param array $data
     * @return string
     */
    public function TopicSubmitAfter($data)
    {
        $oTopic = $data['oTopic'];
        $this->Topic_MergeTopicBlogs($oTopic->getId(), $oTopic->getBlogId());
    }

    /**
     * Шаблонный хук, цепляеться на отображение топика (короткий вид и полный).
     * Генерирует "хлебные крохи" блогов.
     *
     * @return string
     * @param array $data
     */
    public function TemplateTopicShow($aData)
    {
        $oTopic = $aData['topic'];
        $oBlogsTopic = $this->Topic_GetTopicBranches($oTopic);
        $this->Viewer_Assign('aBlogsTree', $oBlogsTopic);
        return $this->Viewer_Fetch(Plugin::GetTemplatePath('treeblogs') . 'actions/ActionTopic/crumbs.tpl');
    }

    public function CheckFields($aData)
    {
        $btnOk = &$aData['bOk'];
        $aForbidenBlogs = $this->Blog_GetBlogOnlyBlogs();
        $aBlogs = getRequest('subblog_id', array());
        if (getRequest('blog_id') == getRequest(0) || getRequest(0) == '-1') {
            $aBlogs[] = getRequest('blog_id');
        }
        foreach ($aBlogs as $sBlogId) {
            if (in_array($sBlogId, $aForbidenBlogs)) {
                $this->Message_AddError(
                        $this->Lang_Get('blog_connect_forbiden_blogs'),
                        $this->Lang_Get('error'));
                $btnOk = false;
            }
        }
    }
}