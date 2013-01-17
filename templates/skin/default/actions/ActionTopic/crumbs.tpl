{foreach from=$aBlogsTree item=oTree name=tree}
    <a href="{$oTree->getUrlFull()}" class="topic-blog">{$oTree->getTitle()|escape:'html'}</a>
    {if $oTree->getType() != 'personal'}
        <a href="#" class="blog-list-info" onclick="return ls.infobox.showInfoBlog(this,{$oTree->getBlogId()});"></a>
    {/if}
{/foreach}
