<div class="<?=$this->cssClass?>">
    <?=$this->image($this->image, 'default', '', $this->imgCssClass)?>
    <? if ($this->showImageCaption) { ?><div><?=(!empty($this->image_caption) ? $this->image_caption : '');?></div><? } ?>
</div>