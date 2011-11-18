{if $aBlogs|@count > 0}
<select name="{$nextlevel}" id="{$groupid}_{$nextlevel}">
        <option value="-1" {if $BlogId==-1 && $smarty.foreach.el2.first}selected="selected"{/if}>{$aLang.no_assign}</option>
        {foreach from=$aBlogs item=oBlog name=el2}
    	    <option {if $BlogId==$oBlog->getId()}selected{/if} value="{$oBlog->getId()}">{$oBlog->getTitle()}{if $oBlog->getBlogsOnly()}*{/if}</option>
        {/foreach}
</select>
{/if}