<? $dimensions = $this->data->getComponent()->getImageDimensions(); ?>
<? $aspectRatio = 0; ?>
<? $width = 0; ?>
<? if (isset($dimensions['width']) && $dimensions['width'] > 0) { ?>
    <? $aspectRatio = $dimensions['height'] / $dimensions['width'] * 100; ?>
    <? $width = 0; ?>
<? } ?>
<div class="<?=$this->cssClass?>"<? if ($this->imageDpr2) { ?> data-dpr2src="<?=$this->imageDpr2?>"<? } ?>
    style="max-width:<?=$width;?>px;">
        <div class="container" style="padding-bottom:<?=$aspectRatio;?>%">
            <?=$this->image($this->image, $this->altText, $this->imgCssClass)?>
        </div>
    <? if ($this->showImageCaption) { ?>
        <div class="imageCaption" style="max-width:<?=$this->imageParam($this->image,'width','default');?>px;"><?=(!empty($this->image_caption) ? $this->image_caption : '');?></div>
    <? } ?>
</div>