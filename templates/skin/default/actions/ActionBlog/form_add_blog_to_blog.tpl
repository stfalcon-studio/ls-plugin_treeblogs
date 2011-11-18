<p id="blog-toblog"
    <label for="parent_id">{$aLang.blog_assign}:</label>
    <select name="parent_id" id="parent_id">
        <option value="0" {if $parentId == 0}selected{/if}>{$aLang.no_assign}</option>
        {foreach from=$aBlogs item=oBlog name=el2}
        <option value="{$oBlog.id}" {if $parentId == $oBlog.id}selected{/if}>{$oBlog.title}</option>
        {/foreach}
    </select>
</p>
{if $oUserCurrent->isAdministrator()}
<p>
    <label for="order_num">{$aLang.blog_order_num}:</label>
    <input type="text" id="order_num" name="order_num" value="{ if !is_null($_aRequest.order_num)}{$_aRequest.order_num}{else}{$oBlogEdit->getOrderNum()}{/if}" class="w100p" /><br />
</p>
<p>
    <input type="checkbox" id="blogs_only" name="blogs_only" class="checkbox" value="1" { if !is_null($_aRequest.blogs_only)}{if $_aRequest.blogs_only==1}checked{/if}{else}{if $oBlogEdit->getBlogsOnly()==1}checked{/if}{/if}/> 
    <label for="blogs_only">{$aLang.blog_blogs_only}</label>
    
</p>
{/if}
