<div class="vpcPostsPostUser">
    Von:
    {if $component.url}
        <a href="{$component.url}">{$component.name}</a>
    {else}
        {$component.name}
    {/if}
    {if $component.created}
    <span> ( Mitglied seit: {$component.created|date_format:"%d.%m.%y, %H:%M"} )</span>
    {/if}
</div>