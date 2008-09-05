<div class="<?=$this->cssClass?>">
    <? if (!$this->isSaved) echo $this->component($this->preview); ?>
    <?= $this->component($this->form); ?>
</div>