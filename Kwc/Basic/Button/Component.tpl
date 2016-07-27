<div class="<?=$this->rootElementClass?>">
    <?=$this->component($this->linkTag)?>
        <span class="<?=$this->bemClass('text').' '.$this->bemClass('text--'.$this->style, $this->style)?>"><?=$this->mailEncodeText($this->text)?></span>
    <?php if ($this->hasContent($this->linkTag)) { ?>
    </a>
    <?php } ?>
</div>
