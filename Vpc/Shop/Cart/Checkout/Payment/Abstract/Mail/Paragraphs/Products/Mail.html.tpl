<p><?=trlpVps('You ordert following product', 'You ordert following products', count($this->items));?>:</p>
<table width="600" class="tblBoxCart" cellspacing="0" cellpadding="0">
    <?
    $c=0;
    foreach ($this->items as $i) { ?>
        <tr class="products<?=($c%2==0 ? ' row1' : ' row2');?>">
            <td width="50" class="amount">
                <font face="Verdana" size="2"><?=$i->row->amount?>x</font>
            </td>
            <td class="product">
                <font face="Verdana" size="2"><?=$i->product->name?></font>
            </td>
            <? foreach($i->additionalOrderData as $d) { ?>
                <td class="<?=$d['class']?>">
                    <font face="Verdana" size="2"><?=$d['name']?>: <?=$d['value']?></font>
                </td>
            <? } ?>
            <td width="200" align="right" class="price">
                <font face="Verdana" size="2"><?=$this->money($i->product->row->price*$i->row->amount, '')?></font>
            </td>
        </tr>
        <? $c++;
    } ?>
</table>
<hr width="600" align="left"/>
<table width="600" class="moneyInfo" cellspacing="0" cellpadding="0">
    <? foreach ($this->sumRows as $row) { ?>
        <tr>
            <td>
                <font face="Verdana" size="2"><?=$row['text']?></font>
            </td>
            <td width="200" align="right">
                <font face="Verdana" size="2"><?=$this->money($row['amount'],'')?></font>
            </td>
        </tr>
    <? } ?>
</table>
