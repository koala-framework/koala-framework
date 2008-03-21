{if $component.url}
{math equation="(compheight - thumbheight) / 2"
        compheight  = $component.thumbMaxHeight
        thumbheight = $component.smallImage.height
        assign = topMargin
}
{assign var=topMargin value=$topMargin|floor}

<a href="{$component.url}"
    rel="enlarge_{$component.width}_{$component.height}"
    title="{if $component.comment}{$component.comment}{/if}"
><img src="{$component.smallImage.url}" style="margin-top:{$topMargin}px;" width="{$component.smallImage.width}" height="{$component.smallImage.height}" /></a>
{/if}