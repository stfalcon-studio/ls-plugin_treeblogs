{if $aTree|@count}
    <div class="block stream">
        <div class="tl"><div class="tr"></div></div>
        <div class="cl"><div class="cr">
        <h1>{$aLang.plugin.treeblogs.block_menutree_title}</h1>
            <div class="menutree">
                <ul class="active level0">
                    {include file="treeblogs-list.tpl" aTree=$aTree level=0}
                </ul>
            </div>
        </div></div>
        <div class="bl"><div class="br"></div></div>
    </div>
{/if}