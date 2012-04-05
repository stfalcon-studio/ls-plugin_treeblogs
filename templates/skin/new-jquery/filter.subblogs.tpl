{if $aBlogsSub}
    <div class="sortThemes">
        {foreach from=$aBlogsSub item=oBlogSub}
        <a class="blogs-filter {if $oBlogSub->getUrl()|in_array:$aBlogFilter}active{/if}"href="{router page='blog'}{$oBlogSub->getUrl()}">{$oBlogSub->getTitle()}</a>
        {/foreach}
    </div>
{/if}