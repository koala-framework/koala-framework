<div class="vpcForumPosts">
    <a class="post" href="{$component.writeUrl}">Eintrag erstellen</a>
    {component component=$component.paging}

    {foreach from=$component.posts item=post}
        {component component=$post}
    {/foreach}

    {if $component.posts}
        {component component=$component.paging}
        <a class="post" href="{$component.writeUrl}">Eintrag erstellen</a>
    {/if}

</div>