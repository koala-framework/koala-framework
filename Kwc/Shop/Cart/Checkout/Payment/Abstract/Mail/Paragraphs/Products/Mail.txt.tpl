<?=$this->data->trlKwf('You ordered the following product', 'You ordered the following products', count($this->items));?>:
<? foreach ($this->items as $item) { ?>
    <?=htmlspecialchars($item->text);?> <? foreach($item->additionalOrderData as $d) { ?><?=htmlspecialchars($d['name']);?>: <?=htmlspecialchars($d['value']);?> <? } ?> <?=htmlspecialchars($this->money($item->price));?>

<? } ?>

<? foreach ($this->sumRows as $row) { ?>
    <?=htmlspecialchars($row['text']);?> <?=htmlspecialchars($this->money($row['amount'],''));?>

<? } ?>
