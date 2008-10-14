<div class="<?=$this->cssClass?>">
    <? if (isset($this->searchForm)) echo $this->component($this->searchForm); ?>
    <? if (isset($this->paging)) echo $this->component($this->paging); ?>
    <? if ($this->formSaved && !count($this->items)) { ?>
        <div class="noEntries"><?= $this->placeholder['noEntriesFound']; ?></div>
    <? } else { ?>
        <?php foreach ($this->items as $item) { ?>
            <div class="product">
                <div class="previewImage"><?=$this->component($item->previewImage);?></div>
                <div class="productName"><?=$this->componentLink($item);?></div>
                <div clasS="previewText"><?=$this->component($item->previewText);?></div>
                <div class="productPrice"><?=$this->money($item->row->price);?></div>
                <?=$this->component($item->addToCart);?>
            </div>
        <?php } ?>
    <? } ?>
    <? if (isset($this->paging)) echo $this->component($this->paging); ?>
</div>