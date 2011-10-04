<div class="<?=$this->cssClass?>">
    <p>
        <?=$this->component($this->linkTag)?>

            <?=$this->mailEncodeText($this->text)?>

        <?if ($this->hasContent($this->linkTag)) {?>
        </a>
        <?}?>
    </p>
</div>
