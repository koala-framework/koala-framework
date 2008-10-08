<div class="<?=$this->cssClass?>">
    <? if (isset($this->searchForm)) echo $this->component($this->searchForm); ?>
    <? if (isset($this->paging)) echo $this->component($this->paging); ?>
    <? if ($this->formSaved && !count($this->items)) { ?>
        <div class="noEntries"><?= $this->placeholder['noEntriesFound']; ?></div>
    <? } else { ?>
        <?php foreach ($this->items as $item) { ?>
            <div>
                <?=$this->component($item->previewImage);?>
                <?=$this->componentLink($item);?>
                <?=$this->component($item->previewText);?>
                <?=$this->money($item->row->price);?>
                <?=$this->component($item->addToCart);?>
            </div>
        <?php } ?>
    <? } ?>
    <? if (isset($this->paging)) echo $this->component($this->paging); ?>
</div>