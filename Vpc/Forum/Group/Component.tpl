<div class="vpcForumGroup">
    <h2>
        <a href="{$component.forumUrl}">{$component.forum}</a> »
        <a href="{$component.groupUrl}">{$component.group}</a>
    </h2>
    <h3>{trlVps text="Topics"}:</h3>
    
    <a class="newThread" href="{$component.newThreadUrl}">{trlVps text="Create a new topic"}</a>
    {component component=$component.paging}
    <ul>
    {foreach from=$component.threads item=t}
        <li class="threads">
            <div class="description">
                <a class="name" href="{$t.url}">{$t.subject} <span>{$t.replies} {trlpVps single="answer" plural="answers" 0=$t.replies}</span></a>
            </div>
           
            <div class="statistik">
                <div class="threads"><strong>{trlVps text="Created by"}:</strong>
                    {if $t.threadUserUrl}
                        <a href="{$t.threadUserUrl}">{$t.threadUser}</a>
                    {else}
                        {$t.threadUser}
                    {/if}
                    <div class="posts"><strong>{trlVps text="on"}:</strong> {$t.threadTime|date_format:"%d.%m.%y, %H:%M"}</div>
                </div>
            </div>

            <div class="lastPost">
            <strong>{trlVps text="Last entry"}:</strong>
                {if $t.postUserUrl}
                    <a href="{$t.postUserUrl}">{$t.postUser}</a>
                {else}
                    {$t.postUser}
                {/if}
                <div class="time"><strong>{trlVps text="on"}:</strong> {$t.postTime|date_format:"%d.%m.%y, %H:%M"}</div>
            </div>
            <div class="clear"></div>
        </li>
        {/foreach}
    </ul>
    
    <a class="newThread" href="{$component.newThreadUrl}">{trlVps text="Create a new topic"}</a>{component component=$component.paging}
</div>