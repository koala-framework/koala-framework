<span class="<?=$this->cssClass;?>">
    <?=$this->component($this->linkTag)?>

        <span><?=$this->mailEncodeText($this->text)?></span>

    <?if ($this->hasContent($this->linkTag)) {?>
    </a>
    <?}?>
</span>
