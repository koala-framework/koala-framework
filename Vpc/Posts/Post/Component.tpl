<div class="vpcPostsPost">
    <div class="lastPoster">
        {component component=$component.user}
        <strong>#{$component.postNum}:</strong>
        <i>{$component.create_time|date_format:"%d.%m.%y, %H:%M"}</i>
        {if $component.editUrl}
            <br /><a href="{$component.editUrl}">{trlVps text="Change Post"}</a>
        {/if}
    </div>
    <div class="clear"></div>
    <div class="comment">
        {$component.content|nl2br}
    </div>
</div>