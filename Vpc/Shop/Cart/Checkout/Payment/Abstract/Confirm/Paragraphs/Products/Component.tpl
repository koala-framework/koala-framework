<? if (isset($this->items)) { ?>
    <p><?=trlpVps('You ordered following product', 'You ordered following products', count($this->items));?>:</p>
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
                <td class="<?=$d['class']?>"><?=$d['name']?>: <?=$d['value']?></td>
            <? } ?>
            <td class="price" colspan="<?=($maxAddOrderData-count($item->additionalOrderData)+1)?>"><?=$this->money($item->price)?></td>
        </tr>
        <? $c++;
    } ?>
    </table>
    <ul class="moneyInfo webListNone">
        <? foreach ($this->sumRows as $row) { ?>
            <li<? if(isset($row['class'])) {?> class="<?=$row['class']?>"<? } ?>>
                <span class="text"><?=$row['text']?></span>
                <span class="price"><?=$this->money($row['amount'],'')?></span>
                <div class="clear"></div>
            </li>
        <? } ?>
    </ul>
<? } else { ?>
    <p><?=trlVps('Productlist');?></p>
<? } ?>
