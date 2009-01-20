<div class="<?=$this->cssClass?>">
    <h1><?=trlVps('Welcome to our shop');?>!</h1>
    <p></p>
    <? if (isset($this->searchForm)) echo $this->component($this->searchForm); ?>
    <? if (isset($this->paging)) echo $this->component($this->paging); ?>
    <? if ($this->formSaved && !count($this->items)) { ?>
        <div class="noEntries"><?= $this->placeholder['noEntriesFound']; ?></div>
    <? } else { ?>
        <?php foreach ($this->items as $item) { ?>
            <div class="product">
                <div class="left">
                    <div class="previewImage"><?=$this->componentLink($item, $this->component($item->previewImage));?></div>
                </div>
                <div class="right">
                    <div class="productName"><?=$item->row->title;?></div>
                    <div class="previewText"><?=$this->component($item->previewText);?></div>
                    <div class="detail"><?=$this->componentLink($item, trlVps('Detail info').' ...');?></div>
                </div>
                <div class="orderStuff">
                    <div class="productPrice"><?=trlVps('EUR')?> <?=$this->money($item->row->price,'');?></div>
                    <?=$this->component($item->addToCart);?>
                </div>
            </div>
        <?php } ?>
    <? } ?>
    <? if (isset($this->paging)) echo $this->component($this->paging); ?>
</div>