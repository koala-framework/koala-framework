{if count($component.pageLinks) > 1}
    {trlVps text="Page"}:
    {foreach from=$component.pageLinks item=l}
        <a href="{$l.href}" rel="{$l.rel}"{if $l.active} class="active"{/if}>{$l.text}</a>
    {/foreach}
{/if}