<div class="<?=$this->rootElementClass?>">
    <? if (isset($this->paging)) echo $this->component($this->paging); ?>
    <?=$this->partials($this->data);?>
    <? if (isset($this->paging)) echo $this->component($this->paging); ?>
</div>