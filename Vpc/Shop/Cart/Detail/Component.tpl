<p><?=trlVps('Product')?>: <?=$this->componentLink($this->product)?></p>
<p><?=trlVps('Price')?>: <?=$this->money($this->product->row->price)?></p>
<?=$this->component($this->form)?>
