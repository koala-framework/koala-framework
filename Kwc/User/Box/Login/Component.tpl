<div class="<?=$this->cssClass?>">
    <?=$this->component($this->form)?>
    <? if ($this->facebook) { ?>
        <?=$this->component($this->facebook)?>.
    <? } ?>
</div>