<div class="vpcImagesEnlarge">
    {foreach from=$component.children item=child}
        <div class="thumbOuter">
            <div class="thumb" style="width:{$component.thumbMaxWidth}px; height:{$component.thumbMaxHeight}px;">
                {component component=$child}
            </div>
        </div>
    {/foreach}
    <div class="clear"></div>
</div>