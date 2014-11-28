<div class="<?=$this->cssClass?>" style="max-width:<?=$this->width;?>px;<? if ($this->defineWidth) {?> width:<?=$this->width;?>px;<? } ?>">
<? if ($this->baseUrl) { ?>
    <?=$this->component($this->linkTag)?>
    <div class="container<? if ($this->width>100) { ?> webResponsiveImgLoading<? } ?>" style="padding-bottom:<?=$this->aspectRatio?>%;"
            data-min-width="<?=$this->minWidth;?>"
            data-max-width="<?=$this->maxWidth;?>"
            data-src="<?=$this->baseUrl;?>">
        <noscript>
            <?=$this->image($this->image, $this->altText, $this->imgCssClass)?>
        </noscript>
    </div>
    <?if ($this->hasContent($this->linkTag)) {?>
    </a>
    <?}?>
    <? if ($this->showImageCaption && !empty($this->image_caption)) { ?>
    <div class="imageCaption" style="max-width:<?=$this->imageParam($this->image,'width','default');?>px;"><?=(!empty($this->image_caption) ? $this->image_caption : '');?></div>
    <? } ?>
<? } ?>
</div>
