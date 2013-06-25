<?=$this->component($this->linkTag)?>
    <?=$this->image($this->data, '', array('class' => $this->imgCssClass, 'border' => '0', 'style' => 'display: block;'))?>
<?if ($this->hasContent($this->linkTag)) {?>
    </a>
<?}?>
<? if ($this->showImageCaption) { ?>
    <div class="imageCaption" style="width:<?=$this->imageParam($this->image,'width','default');?>px;"><?=(!empty($this->image_caption) ? $this->image_caption : '');?></div>
<? } ?>
