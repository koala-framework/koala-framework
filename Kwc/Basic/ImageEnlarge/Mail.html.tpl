<?=$this->component($this->linkTag)?>
    <?=$this->image($this->data, '', array('class' => $this->imgCssClass, 'border' => '0', 'style' => 'display: block;'))?>
<?if ($this->hasContent($this->linkTag)) {?>
    </a>
<?}?>
