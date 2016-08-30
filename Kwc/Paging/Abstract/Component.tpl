<?php if ($this->show) { ?>
    <div class="<?=$this->rootElementClass?>">
        <span><?=$this->placeholders['prefix']?></span>
        <?=$this->partials($this->data, $this->partialParams);?>
        <div class="kwfUp-clear"></div>
    </div>
<?php } ?>
