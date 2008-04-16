{if count($component.pageLinks) > 1}
    {trlVps text="Page"}:
    {foreach from=$component.pageLinks item=l}
        <a href="{$l.href}" rel="{$l.rel}">{$l.text}</a>
    {/foreach}
{/if}