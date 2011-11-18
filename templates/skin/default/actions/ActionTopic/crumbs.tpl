   			{foreach from=$aBlogsTree item=oTree name=tree}
				<ul class="treeblogs">
   				{foreach from=$oTree item=oBlog name=blogs}
					<li><a href="{$oBlog->getUrlFull()}">{$oBlog->getTitle()|escape:'html'}</a>&nbsp;&nbsp;{if !$smarty.foreach.blogs.last}→{/if}</li>
   				{/foreach}
				</ul>
   			{/foreach}
