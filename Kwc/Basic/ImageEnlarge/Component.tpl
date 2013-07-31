<div class="<?=$this->cssClass?>"<? if ($this->imageDpr2) { ?> data-dpr2src="<?=$this->imageDpr2?>"<? } ?>>
    <?=$this->component($this->linkTag)?>
    <?=$this->image($this->image, $this->altText, $this->imgCssClass)?>
    <?if ($this->hasContent($this->linkTag)) {?>
    </a>
    <?}?>
    <? if ($this->showImageCaption && !empty($this->image_caption)) { ?>
    <div class="imageCaption" style="width:<?=$this->imageParam($this->image,'width','default');?>px;"><?=(!empty($this->image_caption) ? $this->image_caption : '');?></div>
    <? } ?>
</div>