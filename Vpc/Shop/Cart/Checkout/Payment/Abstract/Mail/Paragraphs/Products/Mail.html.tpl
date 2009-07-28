<table class="tblBoxCart" cellspacing="0" cellpadding="0">
<?
$c=0;
foreach ($this->items as $i) { ?>
    <tr class="products<?=($c%2==0 ? ' row1' : ' row2');?>">
        <td class="amount"><?=$i->row->amount?>x</td>
        <td class="product"><?=$i->product->name?></td>
        <td class="price"><?=$this->money($i->product->row->price*$i->row->amount, '')?></td>
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
