<div class="forumLatestThreads">
    <ul>
        {foreach from=$component.forumLatestThreads item=t name=tf}
            <li{if $smarty.foreach.tf.index == 0} class="first"{/if}>
                <a href="{$t.url}" title="{$t.subject}">{$t.subject|truncate:70:'...':true}</a>
                <span>({$t.replies} {trlpVps single="answer" plural="answers" 0=$t.replies})<br />
                    {trlVps text="Last entry"} <a href="{$t.postUserUrl}">{$t.postUser}</a>
                    {trlVps text="at"} {$t.postTime|date_format:"%d.%m.%y, %H:%M"}
                    | <a href="{$t.groupUrl}">{$t.groupName}</a>
                </span>
            </li>
        {/foreach}
    </ul>
</div>
