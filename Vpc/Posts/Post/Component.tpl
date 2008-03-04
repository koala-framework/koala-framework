<div class="vpcPostsPost">
    
    <div class="lastPoster">
        {component component=$component.user}
        <strong>#{$component.postNum}:</strong> 
        <i>{$component.create_time|date_format:"%d.%m.%y, %H:%M"}</i>
    </div>
    <div class="clear"></div>
    <div class="comment">
        {$component.content|htmlspecialchars|nl2br}
    </div>
</div>