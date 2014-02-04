<? $dimensions = $this->data->getComponent()->getImageDimensions(); ?>
<? $aspectRatio = 0; ?>
<? $width = 0; ?>
<? if (isset($dimensions['width']) && $dimensions['width'] > 0) { ?>
    <? $aspectRatio = $dimensions['height'] / $dimensions['width'] * 100; ?>
    <? $width = $dimensions['width']; ?>
<? } ?>
<div class="<?=$this->cssClass?>" style="width:<?=$width;?>px;">
    <? $baseUrl = preg_replace("/(\/dh-[0-9]*)\//", "/dh-{width}/", $this->image->getComponent()->getImageUrl()); ?>
    <div class="container" style="padding-bottom:<?=$aspectRatio;?>%;" data-src="<?=$baseUrl;?>">
        <noscript>
            <?=$this->image($this->data, '', $this->imgCssClass)?>
        </noscript>
    </div>
</div>