{if $aTree && $aTree|@count}
    {math equation="x+1" x=$level assign=level}
    {foreach from=$aTree item=branch}
        <li class="level{$level-1}">
            {if $branch.open}
                <div class="{if in_array($branch.blog->getId(),$aTreePath)}active{else}regular{/if}" id="d{$branch.blog->getId()}" onclick="reverseMenu('{$branch.blog->getId()}')"></div>

                <a class="{if $iTreeBlogId == $branch.blog->getId() }active{else}regular{/if}" href="{router page='blogs'}{$branch.blog->getUrl()}">{$branch.blog->getTitle()}</a>
                <ul class="{if in_array($branch.blog->getId(),$aTreePath)}active{else}regular{/if} level{$level}" id="m{$branch.blog->getId()}">
                    {include file="treeblogs-list.tpl" aTree=$branch.child level=$level}
                </ul>
            {elseif (count($branch.child) || $branch.blog->getBlogsOnly())}
                <div class="end"></div>
                <a  class="{if $iTreeBlogId ==$branch.blog->getId() }active{else}regular{/if}"  href="{router page='blogs'}{$branch.blog->getUrl()}">{$branch.blog->getTitle()}</a>
            {else}
                <div class="end"></div>
                <a  class="{if $iTreeBlogId ==$branch.blog->getId() }active{else}regular{/if}"  href="{$branch.blog->getUrlFull()}">{$branch.blog->getTitle()}</a>
            {/if}
        </li>
    {/foreach}
{/if}