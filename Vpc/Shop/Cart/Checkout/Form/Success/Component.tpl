<div class="<?=$this->cssClass?>">
    <div class="receiver">
        <strong><?=$this->order->firstname?> <?=$this->order->lastname?></strong><br />
        <?=$this->order->street?><br />
        <?=$this->order->country?> - <?=$this->order->zip?> <?=$this->order->city?>
    </div>
    <div class="receiverInfo">
        <?=$this->order->email?><br />
        <?=$this->order->phone?>
    </div>
    <table>
        <tr class="firstRow">
            <th class="thProduct"><?=trlVps('Product')?></th>
            <th><?=trlVps('Unit Price')?></th>
            <th><?=trlVps('Amount')?></th>
            <th class="thPrice"><?=trlVps('Price')?></th>
        </tr>
        <? foreach ($this->orderProducts as $op) { ?>
        <? $p = $op->getParentRow('Product') ?>
        <tr>
            <td><?=$p?></td>
            <td><?=trlVps('EUR')?> <?=$this->money($p->price,'')?></td>
            <td><?=$op->amount?></td>
            <td><?=trlVps('EUR')?> <?=$this->money($p->price * $op->amount,'')?></td>
        </tr>
        <tr><td colspan="4"><div class="line"></div></td></tr>
        <? } ?>
        <tr class="subtotal">
            <td colspan="3"><?=trlVps('Subtotal')?>:</td>
            <td><?=trlVps('EUR')?> <?=$this->money($this->order->getSubTotal(),'')?></td>
        </tr>
        <tr>
            <td colspan="3"><?=trlVps('Shipping and Handling')?>:</td>
            <td><?=trlVps('EUR')?> <?=$this->money($this->order->getShipping(),'')?></td>
        </tr>
        <tr class="totalAmount">
            <td colspan="3"><?=trlVps('Total Amount')?>:</td>
            <td class="totalAmountPrice"><?=trlVps('EUR')?> <?=$this->money($this->order->getTotal(),'')?></td>
        </tr>
    </table>
    <div class="confirm"><?=$this->componentLink($this->confirm)?></div>
</div>