<div class="<?=$this->cssClass?>">
    <table class="tblCheckout" cellspacing="0" cellpadding="0">
        <?
        $maxAddOrderData = 0;
        foreach ($this->items as $item) {
            $maxAddOrderData = max($maxAddOrderData, count($item->additionalOrderData));
        }
        $c = count($this->items);
        $i = 1;
        foreach ($this->items as $item) { ?>
            <tr class="products<?=($i%2==1 ? ' row1' : ' row2');?>">
                <td class="product"><?=$item->text?></td>
                <? foreach($item->additionalOrderData as $d) { ?>
                    <td class="<?=$d['class']?>"><?=$d['name']?>: <?=$d['value']?></td>
                <? } ?>
                <td class="price" colspan="<?=($maxAddOrderData-count($item->additionalOrderData)+1)?>"><?=$this->money($item->price)?></td>
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