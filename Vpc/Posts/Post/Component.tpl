<div class="vpcPostsPost">
    {component component=$component.user}
    <div class="lastPoster">
        <strong>#{$component.postNum}:</strong> 
        <div class="time"><i>{$component.create_time|date_format:"%d.%m.%y, %H:%M"}</i></div>
    </div>
    <div class="clear"></div>
    <div class="comment">
        {$component.content|htmlentities|nl2br}
    </div>
</div>

