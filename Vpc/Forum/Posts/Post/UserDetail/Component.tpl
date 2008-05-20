<div class="vpcPostsPostUser">
    {trlVps text="By"}:
    {if $component.url}
        <a href="{$component.url}">{$component.name}</a>
    {else}
        {$component.name}
    {/if}
    {if $component.created}
        {if $component.rating}
        {section name=rating start=0 loop=$component.rating}
            <img src="/assets/web/images/btnPfoten.jpg" width="10" height="10" alt="2 von 5 Pfoten" />
        {/section}
        {/if}
    {/if}
    <br />
    {if $component.isModerator}Moderator{/if}

</div>