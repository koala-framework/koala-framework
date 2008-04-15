<div class="vpcForumPosts">
    <h2>
        <a href="{$component.forumUrl}">{$component.forum}</a> »
        <a href="{$component.groupUrl}">{$component.group|truncate:30:'...':true}</a> »
        <a href="{$component.threadUrl}" title="{$component.thread}">{$component.thread|truncate:30:'...':true}</a>
    </h2>
    
    {component component=$component.observe}
    <a class="post" href="{$component.writeUrl}">{trlVps text="create answer"}</a>
    
    {foreach from=$component.posts item=post}
        {component component=$post}
    {/foreach}
    {component component=$component.observe}
    <a class="post" href="{$component.writeUrl}">{trlVps text="create answer"}</a>
    

</div>