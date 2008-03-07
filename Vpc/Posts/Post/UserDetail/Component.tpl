<div class="vpcPostsPostUser">
    Von:
    {if $component.name}
        {$component.name}
    {else}
        -
    {/if}
    {if $component.created}
        <span> ( Mitglied seit: {$component.created|date_format:"%d.%m.%y"} )</span>
    {/if}
</div>