<div class="vpcImagesEnlarge">
    {foreach from=$component.children item=child}
        <div class="thumbOuter">
            <div class="thumb" style="width:{$component.thumbMaxWidth}px; height:{$component.thumbMaxHeight}px;">
                {component component=$child}
            </div>
        </div>
         <div class="{cycle values="one,two,three,four"}"></div>
    {/foreach}
      <div class="clear"></div>
</div>