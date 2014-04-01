<div class="<?=$this->cssClass?>" style="max-width:<?=$this->width;?>px;">
        <div class="container" style="padding-bottom:<?=$this->aspectRatio;?>%"
            data-min-width="<?=$this->minWidth;?>"
            data-max-width="<?=$this->maxWidth;?>"
            data-src="<?=$this->baseUrl;?>">
            <img />
            <noscript>
                <?=$this->image($this->image, $this->altText, $this->imgCssClass)?>
            </noscript>
        </div>
    <? if ($this->showImageCaption) { ?>
        <div class="imageCaption" style="max-width:<?=$this->imageParam($this->image,'width','default');?>px;"><?=(!empty($this->image_caption) ? $this->image_caption : '');?></div>
    <? } ?>
</div>