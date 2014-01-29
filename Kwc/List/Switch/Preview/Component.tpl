<? $dimensions = $this->data->getComponent()->getImageDimensions(); ?>
<? $aspectRatio = 0; ?>
<? $width = 0; ?>
<? if (isset($dimensions['width']) && $dimensions['width'] > 0) { ?>
    <? $aspectRatio = $dimensions['height'] / $dimensions['width'] * 100; ?>
    <? $width = $dimensions['width']; ?>
<? } ?>
<div class="<?=$this->cssClass?>" style="width:<?=$width;?>px;">
    <div class="container" style="padding-bottom:<?=$aspectRatio;?>%;">
        <?=$this->image($this->data, '', $this->imgCssClass)?>
    </div>
</div>