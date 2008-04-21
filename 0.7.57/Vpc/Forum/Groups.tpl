{* eigenes template wegen rekursion *}
<ul>
{foreach from=$groups item=g}
    <li class="{if $g.post}post{else}title{/if}">
        {if $g.post}
            <div class="description">
                <a class="name" href="{$g.url}">{$g.name}</a>
                <p>{$g.description}</p>
            </div>

            <div class="lastPost">
                <div class="statistik">
                    <div class="threads"><strong>{trlVps text="Topics"}:</strong> {$g.numThreads}</div>
                    <div class="posts"><strong>{trlVps text="Entries"}:</strong> {$g.numPosts}</div>
                </div>
                {trlVps text="Last Entry"}:
                {if $g.lastPostSubject}
                    <a href="{$g.lastPostUrl}" title="{$g.lastPostSubject}">{$g.lastPostSubject|truncate:37:'...':true}</a>
                    <div class="time">
                        <i>am <strong>{$g.lastPostTime|date_format:"%d.%m.%y, %H:%M"}</strong></i> von
                        {if $g.lastPostUserUrl}
                            <a href="{$g.lastPostUserUrl}">{$g.lastPostUser}</a>
                        {else}
                            {$g.lastPostUser}
                        {/if}
                    </div>
                {else}
                    -
                    <div class="time">&nbsp;</div>
                {/if}
            </div>
        {else}
            {$g.name}
        {/if}
        {if $g.childGroups}
            {include file=$component.groupsTemplate groups=$g.childGroups}
        {/if}
    </li>
{/foreach}
</ul>