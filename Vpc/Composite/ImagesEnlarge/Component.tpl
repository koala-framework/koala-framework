<div class="vpcImagesEnlarge">
    {foreach from=$component.children item=child}
        {math equation="(compheight - thumbheight) / 2"
              compheight  = $component.thumbMaxHeight
              thumbheight = $child.smallImage.height
              assign = topMargin
        }
        {assign var=topMargin value=$topMargin|floor}

        <div class="thumbOuter">
            <div class="thumb"
                style="width: {$component.thumbMaxWidth}px;
                    height: {$component.thumbMaxHeight-$topMargin}px;
                    margin-top: {$topMargin}px;"
            >
                {component component=$child}
            </div>
        </div>
    {/foreach}
    <div class="clear"></div>
</div>