<div class="vpcPosts">
    <a class="post" href="{$component.writeUrl}">Antwort erstellen</a>
    {foreach from=$component.posts item=post}
        {component component=$post}
    {/foreach}
    <a class="post" href="{$component.writeUrl}">Antwort erstellen</a>
</div>