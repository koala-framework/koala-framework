<div class="vpcPostsPost">

    <div class="lastPoster">
        <div class="postData">
            {component component=$component.user}
            <strong>#{$component.postNum}:</strong>
            <i>{$component.create_time|date_format:"%d.%m.%y, %H:%M"}</i>
            {if $component.editUrl || $component.deleteUrl || $component.reportUrl || !$component.threadClosed}
                <br />{trlVps text="Post"}:
                {if $component.editUrl}
                    <a href="{$component.editUrl}">{trlcVps context="forum" text="edit"}</a>
                {/if}
                {if $component.editUrl && $component.deleteUrl}|{/if}
                {if $component.deleteUrl}
                    {** todo: löschen schöner machen, evtl eigene seite **}
                    <a href="{$component.deleteUrl}" rel="forumDeleteConfirmation">{trlVps text="delete"}</a>
                {/if}
                {if $component.deleteUrl && $component.reportUrl}|{/if}
                {if $component.reportUrl}
                    <a href="{$component.reportUrl}">melden</a>
                {/if}
                {if $component.reportUrl && !$component.threadClosed}
                    |
                {/if}
                {if !$component.threadClosed}
                    <a href="{$component.writeUrl}">zitieren</a>
                {/if}
            {/if}
        </div>
    </div>
    <div class="comment">
        {$component.content|nl2br}
    </div>
    {if $component.signature}
        <p class="signature"><tt>--<br />{$component.signature|htmlspecialchars|nl2br}</tt></p>
    {/if}
</div>

