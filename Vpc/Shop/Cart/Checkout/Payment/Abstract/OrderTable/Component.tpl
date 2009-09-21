<div class="<?=$this->cssClass?>">
    <table class="tblCheckout" cellspacing="0" cellpadding="0">
        <tr class="firstRow">
            <th class="product"><?=trlVps('Product')?></th>
            <th class="unitPrice"><?=trlVps('Unit Price')?></th>
            <th class="amount"><?=trlVps('Amount')?></th>
            <? foreach($this->additionalOrderDataHeaders as $h) { ?>
            <th class="<?=$h['class']?>"><?=$h['text']?></th>
            <? } ?>
            <th class="price"><?=trlVps('Price')?></th>
        </tr>
        <tr class="empty first">
            <td colspan="<?=(4+count($this->additionalOrderDataHeaders))?>">&nbsp;</td>
        </tr>
        <?
        $c = count($this->items);
        $i = 1;
        foreach ($this->items as $item) { ?>
            <tr class="products<?=($i%2==1 ? ' row1' : ' row2');?>">
                <td class="product"><?=$item->product->name?></td>
                <td class="unitPrice"><?=$this->money($item->row->price,'')?></td>
                <td class="amount"><?=$item->row->amount?></td>
                <? foreach($item->additionalOrderData as $d) { ?>
                    <td class="<?=$d['class']?>"><?=$d['value']?></td>
                <? } ?>
                <td class="price"><?=$this->money($item->row->price * $item->row->amount,'')?></td>
            </tr>
            <tr class="<?=($c==$i ? 'lastline' : 'line');?>">
                <td colspan="<?=(4+count($this->additionalOrderDataHeaders))?>">
                    <div class="line"></div>
                </td>
            </tr>
            <? if($c==$i) { ?>
                <tr class="empty last">
                    <td colspan="2">&nbsp;</td>
                </tr>
            <? }
            $i++;
        } ?>
        <tr>
            <td colspan="<?=(4+count($this->additionalOrderDataHeaders))?>">
                <table class="tblCheckoutPrice" cellspacing="0" cellpadding="0">
                    <? foreach ($this->sumRows as $row) { ?>
                        <tr<? if(isset($row['class'])) {?> class="<?=$row['class']?>"<? } ?>>
                            <td><?=$row['text']?></td>
                            <td class="price"><?=$this->money($row['amount'],'')?></td>
                        </tr>
                    <? } ?>
                </table>
                <div class="clear"></div>
            </td>
        </tr>
    </table>
</div>