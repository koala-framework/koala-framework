<div class="vpcForumGroup">
    <h2>
        <a href="{$component.forumUrl}">{$component.forum}</a> »
        <a href="{$component.groupUrl}">{$component.group}</a>
    </h2>
    <h3>Themen:</h3>

    <p><a class="newThread" href="{$component.newThreadUrl}">Neues Thema erstellen</a></p>

    {component component=$component.paging}
    <ul>
    {foreach from=$component.threads item=t}
        <li class="threads">
            <div class="description">
                <a class="name" href="{$t.url}">{$t.subject} <span>Antworten: {$t.replies}</span></a>
            </div>
           
            <div class="statistik">
                <div class="threads"><strong>Erstellt von:</strong>
                    {if $t.threadUserUrl}
                        <a href="{$t.threadUserUrl}">{$t.threadUser}</a>
                    {else}
                        {$t.threadUser}
                    {/if}
                    <div class="posts"><strong>Datum:</strong> {$t.threadTime|date_format:"%d.%m.%y, %H:%M"}</div>
                </div>
            </div>

            <div class="lastPost">{trlVps text="Last entry"}:
                {if $t.postUserUrl}
                    <a href="{$t.postUserUrl}">{$t.postUser}</a>
                {else}
                    {$t.postUser}
                {/if}
                <div class="time">am <strong><i>{$t.postTime|date_format:"%d.%m.%y, %H:%M"}</i></strong></div>
            </div>
            <div class="clear"></div>
        </li>
        {/foreach}
    </ul>
    
    <p><a class="newThread" href="{$component.newThreadUrl}">Neues Thema erstellen</a></p>
</div>