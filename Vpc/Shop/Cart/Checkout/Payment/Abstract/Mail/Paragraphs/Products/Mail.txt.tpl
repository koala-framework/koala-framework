<?=trlVps('You ordered following product', 'You ordered following products', count($this->items));?>:
<? foreach ($this->items as $i) { ?>
    <?=$i->row->amount?>x <?=$i->product->name?> <?=$this->money($i->row->price*$i->row->amount, '')?>
<? } ?>
<? foreach ($this->sumRows as $row) { ?>
    <?=$row['text']?> <?=$this->money($row['amount'],'')?>
<? } ?>
