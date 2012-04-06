<div class="<?=$this->cssClass?>">
    <?=$this->component($this->linkTag)?>
        <?=$this->image($this->data, '', array('class' => $this->imgCssClass, 'border' => '0'))?>
    <?if ($this->hasContent($this->linkTag)) {?>
        </a>
    <?}?>
</div>
