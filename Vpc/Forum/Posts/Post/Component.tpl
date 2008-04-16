<div class="vpcPostsPost">

    <div class="lastPoster">
        <div class="avatar">
            {if $component.avatarUrl}
                <a href="{$component.userUrl}"><img src="{$component.avatarUrl}" alt="Avatar" /></a>
            {/if}
        </div>
        <div class="postData">
            {component component=$component.user}
            <strong>#{$component.postNum}:</strong>
            <i>{$component.create_time|date_format:"%d.%m.%y, %H:%M"}</i>
            <a href="{$component.writeUrl}" class="quoteLink">Beitrag zitieren</a>
            {if $component.editUrl || $component.deleteUrl}
                <br />{trlVps text="Post"}:
                {if $component.editUrl}
                    <a href="{$component.editUrl}">{trlcVps context="forum" text="edit"}</a>
                {/if}
                {if $component.deleteUrl}
                    | <a href="{$component.deleteUrl}">{trlVps text="delete"}</a>
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

