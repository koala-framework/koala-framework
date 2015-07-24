<div class="<?=$this->rootElementClass?>">
    <?=$this->component($this->linkTag)?>
        <span class="<?=$this->style?>"><?=$this->mailEncodeText($this->text)?></span>
    <?if ($this->hasContent($this->linkTag)) {?>
    </a>
    <?}?>
</div>
