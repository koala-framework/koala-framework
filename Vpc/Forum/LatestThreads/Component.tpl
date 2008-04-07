<div class="forumLatestThreads">
    <ul>
        {foreach from=$component.forumLatestThreads item=t name=tf}
            <li{if $smarty.foreach.tf.index == 0} class="first"{/if}>
                <div class="avatar">
                    {if $t.postUserAvatarUrl}
                        <img src="{$t.postUserAvatarUrl}" alt="Avatar" />
                    {else}
                        &nbsp;
                    {/if}
                </div>
                <div class="thread">
                    <a href="{$t.url}" title="{$t.subject}">{$t.subject|truncate:70:'...':true}</a>
                    <span>({$t.replies} {trlpVps single="answer" plural="answers" 0=$t.replies})<br />
                        {trlVps text="Last entry by"} <a href="{$t.postUserUrl}" title="{$t.postUser}">{$t.postUser|truncate:15:'...':true}</a>
                        {trlVps text="on"} {$t.postTime|date_format:"%d.%m.%y"}
                        {trlVps text="at"} {$t.postTime|date_format:"%H:%M"}
                        | <a href="{$t.groupUrl}" title="{$t.groupName}">{$t.groupName|truncate:29:'...':true}</a>
                    </span>
                </div>
            </li>
        {/foreach}
    </ul>
</div>