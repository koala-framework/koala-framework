<div class="<?=$this->cssClass?>">
    <h1><?=trlVps('Please check your petitions');?></h1>
    <div class="receiver">
        <strong><?=$this->order->title?> <?=$this->order->firstname?> <?=$this->order->lastname?></strong><br />
        <?=$this->order->street?><br />
        <?=$this->order->country?> - <?=$this->order->zip?> <?=$this->order->city?>
    </div>
    <div class="receiverInfo">
        <?=$this->order->email?><br />
        <?=$this->order->phone?>
    </div>
    <div class="receiverComment">
        <? if ($this->order->comment) { ?>
            <?=trlVps('Your Comment')?>:<br />
            <?=$this->order->comment?>
        <? } ?>
    </div>
    <div class="orderInfo">
        <?=trlVps('You pay by')?>
        <? if ($this->order->getCashOnDeliveryCharge()) { ?>
            <?=trlVps('cashOnDelivery')?>
        <? } else {?>
            <?=trlVps('prepayment')?>
        <? } ?>
    </div>
    <table>
        <tr class="firstRow">
            <th class="thProduct"><?=trlVps('Product')?></th>
            <th><?=trlVps('Unit Price')?></th>
            <th><?=trlVps('Amount')?></th>
            <th><?=trlVps('Size')?></th>
            <th class="thPrice"><?=trlVps('Price')?></th>
        </tr>
        <? foreach ($this->orderProducts as $op) { ?>
        <? $p = $op->getParentRow('Product') ?>
        <tr>
            <td><?=$p?></td>
            <td><?=trlVps('EUR')?> <?=$this->money($p->price,'')?></td>
            <td><?=$op->amount?></td>
            <td><?=$op->size?></td>
            <td class="productPrice price"><?=trlVps('EUR')?> <?=$this->money($p->price * $op->amount,'')?></td>
        </tr>
        <tr><td colspan="5"><div class="line"></div></td></tr>
        <? } ?>
        <tr class="subtotal">
            <td colspan="3"><?=trlVps('Subtotal')?>:</td>
            <td colspan="2" class="price"><?=trlVps('EUR')?> <?=$this->money($this->order->getSubTotal(),'')?></td>
        </tr>
        <tr>
            <td colspan="3"><?=trlVps('Shipping and Handling')?>:</td>
            <td colspan="2" class="price"><?=trlVps('EUR')?> <?=$this->money($this->order->getShipping(),'')?></td>
        </tr>
        <? if ($this->order->getCashOnDeliveryCharge()) { ?>
        <tr>
            <td colspan="3"><?=trlVps('Cash on Delivery Charge')?>:</td>
            <td colspan="2" class="price"><?=trlVps('EUR')?> <?=$this->money($this->order->getCashOnDeliveryCharge(),'')?></td>
        </tr>
        <? } ?>
        <tr class="totalAmount">
            <td colspan="3"><?=trlVps('Total Amount')?>:</td>
            <td colspan="2" class="totalAmountPrice"><?=trlVps('EUR')?> <?=$this->money($this->order->getTotal(),'')?></td>
        </tr>
    </table>
    <?=$this->component($this->confirmLink)?>
</div>