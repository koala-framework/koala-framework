<div class="vpcPostsPostUser">
    {trlvps text="Of"}:
    {if $component.name}
        {$component.name}
    {else}
        -
    {/if}
    {if $component.created}
        <span> ( {trlVps text="Mitglied seit"}: {$component.created|date_format:"%d.%m.%y"} )</span>
    {/if}
</div>