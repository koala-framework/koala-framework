<div class="vpcPostsPostUser">
    {trlVps text="of"}:
    {if $component.url}
        <a href="{$component.url}">{$component.name}</a>
    {else}
        {$component.name}
    {/if}
    {if $component.created}
    <span> ( {trlVps text="Member since"}: {$component.created|date_format:"%d.%m.%y"} )</span>
    {/if}
</div>