<div class="<?=$this->rootElementClass?>">
    <?php foreach ($this->children as $c) { ?>
        <?php if ($this->hasContent($c)) { ?>
            <?= $this->component($c); ?>
        <?php } ?>
    <?php } ?>
    <div class="kwfUp-clear"></div>
</div>
