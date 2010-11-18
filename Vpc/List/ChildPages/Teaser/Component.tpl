<div class="<?=$this->cssClass?>">
    <? foreach ($this->children as $c) { ?>
        <?= $this->ifHasContent($c); ?>
            <?= $this->component($c); ?>
        <?= $this->ifHasContent(); ?>
    <? } ?>
</div>
