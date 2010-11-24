<?=trlVps('You ordered following product', 'You ordered following products', count($this->items));?>:
<? foreach ($this->items as $item) { ?>
    <?=$item->text?> <? foreach($item->additionalOrderData as $d) { ?><?=$d['name']?>: <?=$d['value']?> <? } ?> <?=$this->money($item->price)?>

<? } ?>

<? foreach ($this->sumRows as $row) { ?>
    <?=$row['text']?> <?=$this->money($row['amount'],'')?>

<? } ?>
