<div class="<?=$this->rootElementClass?>">
    <?php if (isset($this->searchForm)) echo $this->component($this->searchForm); ?>
    <?=$this->partials($this->data);?>
    <?php if (isset($this->paging)) echo $this->component($this->paging); ?>
</div>
