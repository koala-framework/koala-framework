<div class="<?=$this->rootElementClass?> kwfFadeElements">
    <input type="hidden" name="fadeSelector" class="fadeSelector" value="<?= $this->selector; ?>" />

    <? foreach($this->keys as $k) { ?>
        <?=$this->component($this->$k)?>
    <? } ?>
</div>
