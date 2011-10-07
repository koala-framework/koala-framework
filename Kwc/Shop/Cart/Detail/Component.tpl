<div class="product"><?=$this->placeholder['product'];?><?=$this->componentLink($this->product, $this->text)?></div>
<div class="amount"><?=$this->component($this->form)?></div>
<div class="price"><?=$this->placeholder['unitPrice'];?><strong><?=$this->money($this->price,'')?></strong></div>
<div class="delete">
    <button type="submit" name="<?=$this->data->componentId?>-delete" value="delete"><?=trlVps('Delete')?></button>
</div>
<div class="clear"></div>
