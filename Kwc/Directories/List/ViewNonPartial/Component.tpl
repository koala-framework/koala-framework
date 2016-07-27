<div class="<?=$this->rootElementClass?>">
    <?php if (isset($this->searchForm)) echo $this->component($this->searchForm); ?>
    <?php if (isset($this->paging)) echo $this->component($this->paging); ?>
    <?php if ($this->formSaved && !count($this->items)) { ?>
        <div class="noEntries"><?= $this->placeholder['noEntriesFound']; ?></div>
    <?php } else { ?>
        <?php foreach ($this->items as $item) { ?>
            <?=$this->component($item);?>
        <?php } ?>
    <?php } ?>
    <?php if (isset($this->paging)) echo $this->component($this->paging); ?>
</div>
