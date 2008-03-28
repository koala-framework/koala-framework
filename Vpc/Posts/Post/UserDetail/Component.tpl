<div class="vpcPostsPostUser">
    {trlVps text="By"}:
    {if $component.name}
        {$component.name}
    {else}
        -
    {/if}
    {if $component.created}
        <span> ( {trlVps text="Member since"}: {$component.created|date_format:"%d.%m.%y"} )</span>
    {/if}
</div>