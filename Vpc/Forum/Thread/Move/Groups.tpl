{* eigenes template wegen rekursion *}
<ul>
{foreach from=$groups item=g}
    <li class="{if $g.post}post{else}title{/if}">
        {if $g.post}
            <div class="description">
                <a class="name" href="{$g.moveUrl}">{$g.name}</a>
            </div>
        {else}
            {$g.name}
        {/if}
        {if $g.children}
            {include file=$component.groupsTemplate groups=$g.children}
        {/if}
    </li>
{/foreach}
</ul>