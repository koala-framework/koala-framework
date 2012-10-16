<?=$this->image($this->data, '', array('border' => 0, 'style' => 'display: block;'))?>
<? if ($this->showImageCaption) { ?>
        <div class="imageCaption" style="width:<?=$this->imageParam($this->image,'width','default');?>px;"><?=(!empty($this->image_caption) ? $this->image_caption : '');?></div>
<? } ?>
