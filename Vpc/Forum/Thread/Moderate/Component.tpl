<? if ($this->mayModerate) { ?>
<div class="<?=$this->cssClass?>">
    <fieldset>
        <legend><?= trlVps('Moderate Thread') ?></legend>
        <?= $this->componentLink($this->move); ?> | <?= $this->component($this->close) ?>
    </fieldset>
</div>
<? } ?>