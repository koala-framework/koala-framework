<?php if ($this->baseUrl) { ?>
    <?=$this->image($this->data, $this->altText, array('border' => 0, 'style' => 'display: block;'))?>
    <?php if ($this->showImageCaption) { ?>
            <div class="imageCaption" style="max-width:<?=$this->imageParam($this->image,'width','default');?>px;"><?=(!empty($this->image_caption) ? $this->image_caption : '');?></div>
    <?php } ?>
<?php } ?>
