<div id="groups">
{foreach from=$aGroup item=group  name=grps}
	{assign var="idxi" value=$smarty.foreach.grps.index}
	{assign var="idxi_next" value=$idxi+1}

	<div id="g{$idxi}" class="group">
		<input type="hidden" name="{if $idxi == 0}blog_id{else}subblog_id[{$idxi}]{/if}" id="{if $idxi == 0}blog_id{else}subblog_id_{$idxi}{/if}" value="{$group.iBlogId}"></input>
		{if $idxi > 0}
		<a href="thisAncor{$idxi}"></a><a href="#thisAncor{$idxi}" onclick="delGroup({$idxi})">{$aLang.del_group}</a>
		{/if}
		
		{assign var="idxj" value=0}
		{foreach from=$group.aoLevelBlogs item=oBlogs name=slct}
                    {assign var="idxj" value=$smarty.foreach.slct.index}
                    {if $oBlogs|@count>0}    
                        <select name="{$idxj}" id="g{$idxi}_{$idxj}">
                            {if (!($idxj==0 and $idxi==0) )} 
                                <option value="-1" selected>{$aLang.no_assign}</option>
                            {/if}
                            {foreach from=$oBlogs item=oBlog name=frch}
                                <option {if $oBlog->getId() == $group.aiLevelSelectedBlogId[$idxj] }selected="selected"{/if} value="{$oBlog->getId()}">{$oBlog->getTitle()}{if $oBlog->getBlogsOnly()}*{/if}</option>
                            {/foreach}
                        </select>
                    {/if}
		{/foreach}
            <script>$('g{$idxi}').getElements('select').addEvent('change', changeBlogSelector);</script>	
	</div>
	{assign var="idxi" value=$idxi+1}
{/foreach}

</div>	
<script>nxtGroup={$idxi};</script>
<a href="thisAncor"></a><a href="#thisAncor" onclick="addGroup()">{$aLang.add_group}</a>
