<div class="vpcForum">
    <form class="forumSearch" method="GET" action="{$component.action}">
        <input type="text" name="search" value="{$component.searchText}" />
        <button type="submit">Im Forum suchen</button>
    </form>
    {component component=$component.paging}

    <div class="vpcForumGroup">
    <ul>
        {foreach from=$component.results item=t}
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
    </div>
</div>
