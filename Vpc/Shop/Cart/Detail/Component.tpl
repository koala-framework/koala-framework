<div class="product">
    <div class="productTitle"><?=trlVps('Product')?>: <?=$this->componentLink($this->product)?></div>
    <div class="price"><strong><?=trlVps('EUR')?> <?=$this->money($this->product->row->price,'')?></strong></div>
    <div class="info"><?=$this->component($this->form)?></div>
</div>
