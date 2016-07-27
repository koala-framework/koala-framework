<div class="<?=$this->rootElementClass?>">
    <p>
        <?=$this->component($this->linkTag)?>

            <?=$this->mailEncodeText($this->text)?>

        <?php if ($this->hasContent($this->linkTag)) { ?>
        </a>
        <?php } ?>
    </p>
</div>
