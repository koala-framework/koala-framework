<div class="<?=$this->cssClass;?>">
    <? if (isset($this->items)) { ?>
        <p><?=$this->data->trlpKwf('You ordered the following product', 'You ordered the following products', count($this->items));?>:</p>
        <table class="tblBoxCart" cellspacing="0" cellpadding="0">
        <?
        $maxAddOrderData = 0;
        foreach ($this->items as $item) {
            $maxAddOrderData = max($maxAddOrderData, count($item->additionalOrderData));
        }
        $c=0;
        foreach ($this->items as $item) { ?>
            <tr class="products<?=($c%2==0 ? ' row1' : ' row2');?>">
                <td class="product"><?=$item->text?></td>
                <? foreach($item->additionalOrderData as $d) { ?>
                    <td class="<?=$d['class']?>"><?=$this->data->trlStaticExecute($d['name'])?>: <?=$d['value']?></td>
                <? } ?>
                <td class="price" colspan="<?=($maxAddOrderData-count($item->additionalOrderData)+1)?>"><?=$this->money($item->price)?></td>
            </tr>
            <? $c++;
        } ?>
        </table>
        <ul class="moneyInfo webListNone">
            <? foreach ($this->sumRows as $row) { ?>
                <li<? if(isset($row['class'])) {?> class="<?=$row['class']?>"<? } ?>>
                    <span class="text"><?=$this->data->trlStaticExecute($row['text'])?></span>
                    <span class="price"><?=$this->money($row['amount'],'')?></span>
                    <div class="clear"></div>
                </li>
            <? } ?>
            <? if ($this->tableFooterText) { ?>
                <li class="footer">
                    <?=$this->tableFooterText?>
                </li>
            <? } ?>
        </ul>
    <? } else { ?>
        <p><?=$this->data->trlKwf('Productlist');?></p>
    <? } ?>
</div>
