<div class="vpcPostsPost">
    
    <div class="lastPoster">
        {component component=$component.user}
        <strong>#{$component.postNum}:</strong> 
        <i>{$component.create_time|date_format:"%d.%m.%y, %H:%M"}</i>
        <a href="{$component.writeUrl}" class="quoteLink">Beitrag zitieren</a>
        {if $component.editUrl}
            <br />Moderation: <a href="{$component.editUrl}">Beitrag bearbeiten</a>
        {/if}
        <div class="clear"></div>
    </div>
    <div class="clear"></div>
    <div class="comment">
        {$component.content|nl2br}
    </div>
    {if $component.signature}
        <p class="signature"><tt>--<br />{$component.signature|htmlspecialchars|nl2br}</tt></p>
    {/if}
</div>

