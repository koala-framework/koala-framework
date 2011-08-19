<div class="<?=$this->cssClass?>">
    <? foreach ($this->children as $c) { ?>
        <?if ($this->hasContent($c)) { ?>
            <?= $this->component($c); ?>
        <?}?>
    <? } ?>
	<div class="clear"></div>
</div>
