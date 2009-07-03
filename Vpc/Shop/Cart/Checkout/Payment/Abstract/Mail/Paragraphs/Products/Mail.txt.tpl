<? foreach ($this->items as $i) { ?>
    <?=$i->row->amount?>x <?=$i->product->name?> <?=trlVps('EUR')?> <?=$this->money($i->product->row->price*$i->row->amount, '')?>
<? } ?>
<? foreach ($this->sumRows as $row) { ?>
    <?=$row['text']?> <?=trlVps('EUR')?> <?=$this->money($row['amount'],'')?>
<? } ?>
