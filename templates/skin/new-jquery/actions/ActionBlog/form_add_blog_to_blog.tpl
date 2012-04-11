<p id="blog-toblog">
    <label for="parent_id">{$aLang.blog_assign}:</label>
    <select name="parent_id" id="parent_id">
        <option value="">{$aLang.no_assign}</option>
        {foreach from=$aBlogs item=oBlog name=el2}
            <option value="{$oBlog->getId()}" {if $_aRequest.parent_id == $oBlog->getId()}selected{/if}>{$oBlog->getTitle()}</option>
        {/foreach}
    </select>
</p>
{if $oUserCurrent->isAdministrator()}
    <p>
        <label for="order_num">{$aLang.blog_order_num}:</label>
        <input type="text" id="order_num" name="order_num" value="{$_aRequest.order_num}" class="w100p" /><br />
    </p>
    <p>
        <input type="checkbox" id="blogs_only" name="blogs_only" class="checkbox" value="1" {if $_aRequest.blogs_only==1}checked{/if}/>
        <label for="blogs_only">{$aLang.blog_blogs_only}</label>
    </p>
{/if}
