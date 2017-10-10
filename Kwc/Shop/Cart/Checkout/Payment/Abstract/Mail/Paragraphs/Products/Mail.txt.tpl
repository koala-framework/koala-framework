<?=$this->data->trlKwf('You ordered the following product', 'You ordered the following products', count($this->items));?>:
<?php foreach ($this->items as $item) { ?>
    <?=htmlspecialchars($item->text);?> <?php foreach($item->additionalOrderData as $d) { ?><?=htmlspecialchars($this->data->trlStaticExecute($d['name']));?>: <?=htmlspecialchars($d['value']);?> <?php } ?> <?=$this->money($item->price);?>

<?php } ?>

<?php foreach ($this->sumRows as $row) { ?>
    <?=htmlspecialchars($this->data->trlStaticExecute($row['text']));?> <?=$this->money($row['amount'],'');?>

<?php } ?>
