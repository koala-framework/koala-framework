<div class="vpcPosts">
    {foreach from=$component.posts item=post}
        {component component=$post}
    {/foreach}
    <a href="{$component.writeUrl}">new post</a>
</div>