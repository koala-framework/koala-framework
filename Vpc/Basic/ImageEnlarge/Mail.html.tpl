<?=$this->component($this->linkTag)?>
    <?=$this->image($this->data, 'default', '', array('class' => $this->imgCssClass, 'border' => '0'))?>
<?=$this->ifHasContent($this->linkTag)?></a><?=$this->ifHasContent()?>
<? if ($this->showImageCaption) { ?>
    <div class="imageCaption" style="width:<?=$this->imageParam($this->image,'width','default');?>px;"><?=(!empty($this->image_caption) ? $this->image_caption : '');?></div>
<? } ?>
