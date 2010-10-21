<div class="<?=$this->cssClass?>">
    <?=$this->image($this->image, 'default', '', $this->imgCssClass)?>
    <? if ($this->showImageCaption) { ?>
        <div class="imageCaption" style="width:<?=$this->imageParam($this->image,'width','default');?>px;"><?=(!empty($this->image_caption) ? $this->image_caption : '');?></div>
    <? } ?>
</div>