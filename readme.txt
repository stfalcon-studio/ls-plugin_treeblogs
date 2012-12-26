Плагин Treeblogs

Позволяет создавать дерево блогов.

Плагин переопределяет стандартные шаблоны
	action/ActionTopic/add.tpl
	topic.tpl
	topic_list.tpl

Также необходимо в шаблон отображения топика "templates/skin/(*ваш скин*)/topic_part_header.tpl"
добавить хук:
    {hook run='topic_breadcrumbs_list' topic=$oTopic bTopicList=$bTopicList}

после текста (строка: 30):
    {if $oBlog->getType() != 'personal'}
    <a href="#" class="blog-list-info" onclick="return ls.infobox.showInfoBlog(this,{$oBlog->getId()});"></a>
    {/if}

 результати должен получится текст вида:

    <div class="topic-info">
        <a href="{$oBlog->getUrlFull()}" class="topic-blog">{$oBlog->getTitle()|escape:'html'}</a>
        {if $oBlog->getType() != 'personal'}
            <a href="#" class="blog-list-info" onclick="return ls.infobox.showInfoBlog(this,{$oBlog->getId()});"></a>
        {/if}
        {hook run='topic_breadcrumbs_list' topic=$oTopic bTopicList=$bTopicList}
    </div>

