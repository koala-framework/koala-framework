<p><?=trlpVps('You ordered following product', 'You ordered following products', count($this->items));?>:</p>
<table width="600" class="tblBoxCart" cellspacing="0" cellpadding="0">
    <?
    $c=0;
    foreach ($this->items as $i) { ?>
        <tr class="products<?=($c%2==0 ? ' row1' : ' row2');?>">
            <td width="50" class="amount"><?=$i->row->amount?>x</td>
            <td class="product"><?=$i->product->name?></td>
            <? foreach($i->additionalOrderData as $d) { ?>
                <td class="<?=$d['class']?>"><?=$d['name']?>: <?=$d['value']?></td>
            <? } ?>
            <td width="200" align="right" class="price">
                <?=$this->money($i->product->row->price*$i->row->amount, '')?>
            </td>
        </tr>
        <? $c++;
    } ?>
</table>
<hr width="600" align="left"/>
<table width="600" class="moneyInfo" cellspacing="0" cellpadding="0">
    <? foreach ($this->sumRows as $row) { ?>
        <tr>
            <td><?=$row['text']?></td>
            <td width="200" align="right"><?=$this->money($row['amount'],'')?></td>
        </tr>
    <? } ?>
</table>
