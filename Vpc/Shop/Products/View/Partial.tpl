<div class="product">
    <div class="left">
        <div class="previewImage"><?=$this->componentLink($this->item, $this->component($this->item->previewImage));?></div>
    </div>
    <div class="right">
        <div class="productName"><?=$this->item->row->title;?></div>
        <div class="previewText"><?=$this->component($this->item->previewText);?></div>
        <div class="detail"><?=$this->componentLink($this->item, trlVps('Detail info').' ...');?></div>
    </div>
    <div class="orderStuff">
        <div class="productPrice"><?=trlVps('EUR')?> <?=$this->money($this->item->row->price,'');?></div>
        <?=$this->component($this->item->addToCart);?>
    </div>
</div>
