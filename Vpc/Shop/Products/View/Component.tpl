<div class="<?=$this->cssClass?>">
    <? if (isset($this->searchForm)) echo $this->component($this->searchForm); ?>
    <? if (isset($this->paging)) echo $this->component($this->paging); ?>
    <? if ($this->formSaved && !count($this->items)) { ?>
        <div class="noEntries"><?= $this->placeholder['noEntriesFound']; ?></div>
    <? } else { ?>
        <?php foreach ($this->items as $item) { ?>
            <div>
                <?=$this->componentLink($item);?>
                <?=$this->component($item->addToCart);?>
            </div>
        <?php } ?>
    <? } ?>
    <? if (isset($this->paging)) echo $this->component($this->paging); ?>
</div>