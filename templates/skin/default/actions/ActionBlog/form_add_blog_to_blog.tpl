<p id="blog-toblog">
    <label for="parent_id">{$aLang.plugin.treeblogs.blog_assign}:</label>
    <select name="parent_id" id="parent_id">
        <option value="">{$aLang.plugin.treeblogs.no_assign}</option>
        {foreach from=$aBlogs item=oBlog name=el2}
            <option value="{$oBlog->getId()}" {if $data['parent_id'] == $oBlog->getId()}selected{/if}>{$oBlog->getTitle()}</option>
        {/foreach}
    </select>
</p>
{if $oUserCurrent->isAdministrator()}
    <p>
        <label for="order_num">{$aLang.plugin.treeblogs.blog_order_num}:</label>
        <input type="text" id="order_num" name="order_num" value="{$data['order_num']}" class="input-text input-width-100" /><br />
    </p>
    <p>
        <label><input type="checkbox" id="blogs_only" name="blogs_only" class="checkbox" value="1" {if $data['blogs_only'] ==1}checked{/if}/>
        {$aLang.plugin.treeblogs.blog_blogs_only}</label>
        <small class="note">{$aLang.plugin.treeblogs.blog_blogs_connect_alert}</small>
    </p>
{/if}
