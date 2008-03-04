<div class="vpcPostsPostUser">
    Von:
    {$component.name}
    {if $component.created}
        <span> ( Mitglied seit: {$component.created|date_format:"%d.%m.%y"} )</span>
    {/if}
</div>