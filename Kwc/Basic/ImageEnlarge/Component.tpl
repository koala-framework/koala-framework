<figure class="<?=$this->rootElementClass?> kwfUp-kwcImage" style="<?=$this->style;?>">
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
    <figcaption class="<?=$this->bemClass('imageCaption')?> kwfUp-imageCaption" style="<?=$this->captionStyle;?>"><?=(!empty($this->image_caption) ? $this->image_caption : '');?></figcaption>
    <? } ?>
    <?if ($this->hasContent($this->linkTag)) {?>
    </a>
    <?}?>
<? } ?>
</figure>
