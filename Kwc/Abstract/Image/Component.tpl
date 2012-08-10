<div class="<?=$this->cssClass?>"<? if ($this->imageDpr2) { ?> data-dpr2src="<?=$this->imageDpr2?>"<? } ?>>
    <?=$this->image($this->image, '', $this->imgCssClass)?>
    <? if ($this->showImageCaption) { ?>
        <div class="imageCaption" style="width:<?=$this->imageParam($this->image,'width','default');?>px;"><?=(!empty($this->image_caption) ? $this->image_caption : '');?></div>
    <? } ?>
</div>