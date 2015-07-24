<div class="<?=$this->rootElementClass?>">
    <? if (!$this->isSaved) echo $this->component($this->preview); ?>
    <?=$this->component($this->form)?>
</div>