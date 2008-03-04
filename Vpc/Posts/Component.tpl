<div class="vpcPosts">
    {foreach from=$component.posts item=post}
        {component component=$post}
    {/foreach}
    <a class="post" href="{$component.writeUrl}">Kommentar erstellen</a>
</div>