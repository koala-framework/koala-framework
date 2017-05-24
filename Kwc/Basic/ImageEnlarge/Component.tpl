<div class="<?=$this->cssClass?><? if ($this->showImageCaption && !empty($this->image_caption)) { ?> showImageCaption<? } ?>" style="<?=$this->style;?>">
<? if ($this->baseUrl) { ?>
    <?=$this->component($this->linkTag)?>
    <div class="<?=$this->containerClass?>" style="padding-bottom:<?=$this->aspectRatio?>%;"
            data-min-width="<?=$this->minWidth;?>"
            data-max-width="<?=$this->maxWidth;?>"
            data-src="<?=$this->baseUrl;?>">
        <noscript>
            <?=$this->image($this->image, $this->altText, $this->imgAttributes)?>
        </noscript>
    </div>
    <? if ($this->showImageCaption && !empty($this->image_caption)) { ?>
    <div class="imageCaption" style="max-width:<?=$this->imageParam($this->image,'width','default');?>px;"><?=(!empty($this->image_caption) ? $this->image_caption : '');?></div>
    <? } ?>
    <?if ($this->hasContent($this->linkTag)) {?>
    </a>
    <?}?>
<? } ?>
</div>
