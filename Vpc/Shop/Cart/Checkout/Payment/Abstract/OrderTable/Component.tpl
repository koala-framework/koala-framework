<div class="<?=$this->cssClass?>">
    <table class="tblCheckout" cellspacing="0" cellpadding="0">
        <tr class="firstRow">
            <th class="product"><?=trlVps('Product')?></th>
            <th class="unitPrice"><?=trlVps('Unit Price')?></th>
            <th class="amount"><?=trlVps('Amount')?></th>
            <th class="price"><?=trlVps('Price')?></th>
        </tr>
        <tr class="empty first">
            <td colspan="4">&nbsp;</td>
        </tr>
        <?
        $c = count($this->orderProducts);
        $i = 1;
        foreach ($this->orderProducts as $op) { ?>
            <? $p = $op->getParentRow('Product') ?>
            <tr class="products<?=($i%2==1 ? ' row1' : ' row2');?>">
                <td class="product"><?=$p?></td>
                <td class="unitPrice"><?=trlVps('EUR')?> <?=$this->money($p->price,'')?></td>
                <td class="amount"><?=$op->amount?></td>
                <td class="price"><?=trlVps('EUR')?> <?=$this->money($p->price * $op->amount,'')?></td>
            </tr>
            <tr class="<?=($c==$i ? 'lastline' : 'line');?>">
                <td colspan="4">
                    <div class="line"></div>
                </td>
            </tr>
            <? if($c==$i) { ?>
                <tr class="empty last">
                    <td colspan="4">&nbsp;</td>
                </tr>
            <? }
            $i++;
        } ?>
        <tr>
            <td colspan="4">
                <table class="tblCheckoutPrice" cellspacing="0" cellpadding="0">
                    <? foreach ($this->sumRows as $row) { ?>
                        <tr<? if(isset($row['class'])) {?> class="<?=$row['class']?>"<? } ?>>
                            <td colspan="3"><?=$row['text']?></td>
                            <td class="price"><?=trlVps('EUR')?> <?=$this->money($row['amount'],'')?></td>
                        </tr>
                    <? } ?>
                </table>
                <div class="clear"></div>
            </td>
        </tr>
    </table>
</div>