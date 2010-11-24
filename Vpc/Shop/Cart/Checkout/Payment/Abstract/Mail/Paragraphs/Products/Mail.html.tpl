<table width="100%" cellspacing="0" cellpadding="0">
    <tr>
        <td>
            <?=trlpVps('You ordered following product', 'You ordered following products', count($this->items));?>:
        </td>
    </tr>
</table>
<table width="100%" class="tblBoxCart" cellspacing="0" cellpadding="0">
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
            <td class="price" colspan="<?=($maxAddOrderData-count($item->additionalOrderData)+1)?>" align="right"><?=$this->money($item->price)?></td>
        </tr>
        <? $c++;
    } ?>
</table>
<hr width="100%" align="left"/>
<table width="100%" class="moneyInfo" cellspacing="0" cellpadding="0">
    <? foreach ($this->sumRows as $row) { ?>
        <tr>
            <td align="right"><?=$row['text']?></td>
            <td width="120" align="right"><?=$this->money($row['amount'],'')?></td>
        </tr>
    <? } ?>
</table>
