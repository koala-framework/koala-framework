<div class="<?=$this->rootElementClass?>">
    <? foreach ($this->children as $c) { ?>
        <?if ($this->hasContent($c)) { ?>
            <?= $this->component($c); ?>
        <?}?>
    <? } ?>
	<div class="kwfUp-clear"></div>
</div>
