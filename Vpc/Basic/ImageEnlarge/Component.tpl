<div class="<?=$this->cssClass?>">
    <?=$this->component($this->linkTag)?>
        <?=$this->image($this->image, 'default', '', $this->imgCssClass)?>
    <?=$this->ifHasContent($this->linkTag)?>
        </a>
    <?=$this->ifHasContent()?>
    <? if ($this->showImageCaption && !empty($this->image_caption)) { ?>
        <div class="imageCaption" style="width:<?=$this->imageParam($this->image,'width','default');?>px;"><?=(!empty($this->image_caption) ? $this->image_caption : '');?></div>
    <? } ?>
</div>