<div class="forumLatestThreads">
    <ul>
        {foreach from=$component.forumLatestThreads item=t name=tf}
            <li{if $smarty.foreach.tf.index == 0} class="first"{/if}>
                <div class="avatar">
                    {if $t.postUserAvatarUrl}
                        <a href="{$t.postUserUrl}"><img src="{$t.postUserAvatarUrl}" alt="Avatar" /></a>
                    {else}
                        &nbsp;
                    {/if}
                </div>
                <div class="thread">
                    <a href="{$t.url}" title="{$t.subject}">{$t.subject|truncate:55:'...':true}</a>
                    <span>({$t.replies} {trlpVps single="answer" plural="answers" 0=$t.replies})<br />
                        {trlVps text="Last entry by"} <a href="{$t.postUserUrl}" title="{$t.postUser}">{$t.postUser|truncate:14:'...':true}</a>
                        {trlVps text="on"} {$t.postTime|date_format:"%d.%m.%y"}
                        {trlVps text="at"} {$t.postTime|date_format:"%H:%M"}
                        | <a href="{$t.groupUrl}" title="{$t.groupName}">{$t.groupName|truncate:22:'...':true}</a>
                    </span>
                </div>
            </li>
        {/foreach}
    </ul>
</div>