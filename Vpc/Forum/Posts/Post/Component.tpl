<div class="vpcPostsPost">
    
    <div class="lastPoster">
        {component component=$component.user}
        <strong>#{$component.postNum}:</strong> 
        <i>{$component.create_time|date_format:"%d.%m.%y, %H:%M"}</i>
        {if $component.editUrl}
            <br /><a href="{$component.editUrl}">Beitrag ändern</a>
        {/if}
    </div>
    <div class="clear"></div>
    <div class="comment">
        {$component.content|htmlspecialchars|nl2br}
    </div>
    {if $component.signature}
        <p class="signature"><tt>--<br />{$component.signature|htmlspecialchars|nl2br}</tt></p>
    {/if}
</div>

