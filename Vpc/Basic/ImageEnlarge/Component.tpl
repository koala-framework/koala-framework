<div class="<?=$this->cssClass?>">
    <?=$this->component($this->linkTag)?>
        <?=$this->image($this->image, 'default', '', $this->imgCssClass)?>
    <?if ($this->hasContent($this->linkTag)) {?>
        </a>
    <?}?>
    <? if ($this->showImageCaption && !empty($this->image_caption)) { ?>
        <div class="imageCaption" style="width:<?=$this->imageParam($this->image,'width','default');?>px;"><?=(!empty($this->image_caption) ? $this->image_caption : '');?></div>
    <? } ?>
</div>