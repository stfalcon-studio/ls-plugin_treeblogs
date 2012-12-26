{if $aTree|@count}
    {math equation="x+1" x=$level assign=level}
        {foreach from=$aTree item=branch}
            <li class="level{$level-1}">
                {if $branch.child|@count > 0}
                    <div class="{if in_array($branch.blog->getId(),$aTreePath)}active{else}regular{/if}" id="d{$branch.blog->getId()}" onclick="reverseMenu('{$branch.blog->getId()}')"></div>
                    <a class="{if $iTreeBlogId == $branch.blog->getId() }active{else}regular{/if}" href="{$branch.blog->getUrlFull()}"{if $iTreeBlogId == $branch.blog->getId()}href="{$branch.blog->getUrlFull()}"{else}onclick="reverseMenu('{$branch.blog->getId()}'); return false;"  href="#"{/if}>{$branch.blog->getTitle()}</a>
                    <ul class="{if in_array($branch.blog->getId(),$aTreePath)}active{else}regular{/if} level{$level}" id="m{$branch.blog->getId()}">
                        {include file="treeblogs-level.tpl" aTree=$branch.child level=$level}
                    </ul>
                {else}
                    <div class="end"></div>
                    <a  class="{if $iTreeBlogId ==$branch.blog->getId() }active{else}regular{/if}"  href="{$branch.blog->getUrlFull()}">{$branch.blog->getTitle()}</a>
                {/if}
            </li>
        {/foreach}
{/if}