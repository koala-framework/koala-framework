<div class="<?=$this->rootElementClass?>">
    <?=$this->component($this->form)?>
    <? if ($this->facebook) { ?>
        <?=$this->component($this->facebook)?>.
    <? } ?>
</div>