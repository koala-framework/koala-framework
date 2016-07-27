<span class="<?=$this->rootElementClass?>">
    <?=$this->component($this->linkTag)?>

        <span><?=$this->mailEncodeText($this->text)?></span>

    <?php if ($this->hasContent($this->linkTag)) { ?>
    </a>
    <?php } ?>
</span>
