<?php if ($this->baseUrl) { ?>
    <?=$this->component($this->linkTag)?>
        <?=$this->image($this->data, '', array('class' => $this->imgCssClass, 'border' => '0', 'style' => 'display: inline-block;vertical-align: top'))?>
    <?php if ($this->hasContent($this->linkTag)) { ?>
        </a>
    <?php } ?>
    <?php if ($this->showImageCaption) { ?>
        <div class="imageCaption" style="max-width:<?=$this->imageParam($this->image,'width','default');?>px;"><?=(!empty($this->image_caption) ? $this->image_caption : '');?></div>
    <?php } ?>
<?php } ?>
