<div class="<?=$this->cssClass?>">
    <p>
        <?=$this->component($this->linkTag)?>

            <?=$this->mailEncodeText($this->text)?>

        <?=$this->ifHasContent($this->linkTag)?>
        </a>
        <?=$this->ifHasContent()?>
    </p>
</div>
