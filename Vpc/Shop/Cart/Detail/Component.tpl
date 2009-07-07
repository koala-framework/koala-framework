<div class="product"><?=trlVps('Product')?>: <?=$this->componentLink($this->product)?></div>
<div class="amount"><?=$this->component($this->form)?></div>
<div class="price"><strong><?=trlVps('EUR')?> <?=$this->money($this->product->row->price,'')?></strong></div>
<div class="delete">
    <button type="submit" name="<?=$this->data->componentId?>-delete" value="delete"><?= trlVps('Delete') ?></button>
</div>
<div class="clear"></div>
