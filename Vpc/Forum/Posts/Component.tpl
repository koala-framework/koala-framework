<div class="vpcForumPosts">
    <h2>
        <a href="{$component.forumUrl}">{$component.forum}</a> »
        <a href="{$component.groupUrl}">{$component.group|truncate:30:'...':true}</a> »
        <a href="{$component.threadUrl}" title="{$component.thread}">{$component.thread|truncate:30:'...':true}</a>
    </h2>
    <a class="post" href="{$component.writeUrl}">Antwort erstellen</a>
    {foreach from=$component.posts item=post}
        {component component=$post}
    {/foreach}
    <a class="post" href="{$component.writeUrl}">Antwort erstellen</a>
</div>