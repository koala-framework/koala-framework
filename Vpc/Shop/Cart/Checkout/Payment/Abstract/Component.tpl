<div class="<?=$this->cssClass?>">
    <h1><?=trlVps('Please check your petitions');?></h1>
    <div class="receiver">
        <p>
            <strong><?=$this->order->title?> <?=$this->order->firstname?> <?=$this->order->lastname?></strong><br />
            <?=$this->order->street?><br />
            <?=$this->order->country?> - <?=$this->order->zip?> <?=$this->order->city?>
        </p>
    </div>
    <div class="receiverInfo">
        <p>
            <?=$this->order->email?><br />
            <?=$this->order->phone?>
        </p>
    </div>
    <div class="receiverComment">
        <p>
            <? if ($this->order->comment) { ?>
                <strong><?=trlVps('Your Comment')?></strong><br />
                <?=$this->order->comment?>
            <? } ?>
        </p>
    </div>
    <div class="orderInfo">
        <p>
            <?=trlVps('You pay by')?> <?=$this->paymentTypeText?>
        </p>
    </div>
    <table class="tblCheckout" cellspacing="0" cellpadding="0">
        <tr class="firstRow">
            <th class="product"><?=trlVps('Product')?></th>
            <th class="unitPrice"><?=trlVps('Unit Price')?></th>
            <th class="amount"><?=trlVps('Amount')?></th>
            <th class="price"><?=trlVps('Price')?></th>
        </tr>
        <?
        $c = count($this->orderProducts);
        $i = 1;
        foreach ($this->orderProducts as $op) { ?>
            <? $p = $op->getParentRow('Product') ?>
            <? if($i==1) { ?>
                <tr class="empty">
                    <td colspan="4">&nbsp;</td>
                </tr>
            <? } ?>
            <? echo "<pre>"; print_r($p); echo "</pre>"; ?>
            <tr class="products<?=($i%2==1 ? ' row1' : ' row2');?>">
                <td class="product"><?=$p?></td>
                <td class="unitPrice"><?=trlVps('EUR')?> <?=$this->money($p->price,'')?></td>
                <td class="amount"><?=$op->amount?></td>
                <td class="price"><?=trlVps('EUR')?> <?=$this->money($p->price * $op->amount,'')?></td>
            </tr>
            <? if($c==$i) { ?>
                <tr class="empty">
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
    <?=$this->component($this->confirmLink)?>
</div>