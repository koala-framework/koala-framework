<?=$this->data->trlKwf('You ordered the following product', 'You ordered the following products', count($this->items));?>:
<?php foreach ($this->items as $item) { ?>
    <?=Kwf_Util_HtmlSpecialChars::filter($item->text);?> <?php foreach($item->additionalOrderData as $d) { ?><?=Kwf_Util_HtmlSpecialChars::filter($this->data->trlStaticExecute($d['name']));?>: <?=Kwf_Util_HtmlSpecialChars::filter($d['value']);?> <?php } ?> <?=Kwf_Util_HtmlSpecialChars::filter($this->money($item->price));?>

<?php } ?>

<?php foreach ($this->sumRows as $row) { ?>
    <?=Kwf_Util_HtmlSpecialChars::filter($this->data->trlStaticExecute($row['text']));?> <?=Kwf_Util_HtmlSpecialChars::filter($this->money($row['amount'],''));?>

<?php } ?>
