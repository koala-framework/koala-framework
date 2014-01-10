<span class="<?=$this->cssClass;?>">
    <?=$this->component($this->linkTag)?>

        <?=$this->mailEncodeText($this->text)?>

    <?if ($this->hasContent($this->linkTag)) {?>
    </a>
    <?}?>
</span>
