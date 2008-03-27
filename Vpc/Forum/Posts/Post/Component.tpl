<div class="vpcPostsPost">

    <div class="lastPoster">
        <div class="avatar">
            {if $component.avatarUrl}
                <img src="{$component.avatarUrl}" alt="Avatar" />
            {/if}
        </div>
        <div class="postData">
            {component component=$component.user}
            <strong>#{$component.postNum}:</strong>
            <i>{$component.create_time|date_format:"%d.%m.%y, %H:%M"}</i>
            <a href="{$component.writeUrl}" class="quoteLink">Beitrag zitieren</a>
            {if $component.editUrl}
                <br />{trlVps text="Moderation"}: <a href="{$component.editUrl}">{trlVps text="Edit entry"}</a>
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

