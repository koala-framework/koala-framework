<div class="<?=$this->rootElementClass?>">
    <?php if (isset($this->searchForm)) echo $this->component($this->searchForm); ?>
    <?php if (isset($this->paging)) echo $this->component($this->paging); ?>
    <?php if ($this->formSaved && !count($this->items)) { ?>
        <div class="noEntries"><?= $this->placeholder['noEntriesFound']; ?></div>
    <?php } else { ?>
        <?=$this->partials($this->data)?>
    <?php } ?>
    <?php if (isset($this->paging)) echo $this->component($this->paging); ?>
</div>
