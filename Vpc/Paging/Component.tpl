{if count($component.pageLinks) > 1}
<div class="vpcPaging">
    <span>{trlVps text="Page"}:</span> 
    {foreach from=$component.pageLinks item=l}
        <a href="{$l.href}" rel="{$l.rel}"{if $l.active} class="active"{/if}>{$l.text}</a>
    {/foreach}
</div>
{/if}