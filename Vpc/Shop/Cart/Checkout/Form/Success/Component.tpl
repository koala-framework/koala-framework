<div class="<?=$this->cssClass?>">
    <p>
        <?=$this->order->firstname?> <?=$this->order->lastname?><br />
        <?=$this->order->street?><br />
        <?=$this->order->zip?> <?=$this->order->city?><br />
        <?=$this->order->country?>
    </p>

    <p>
        <?=$this->order->email?><br />
        <?=$this->order->phone?>
    </p>

    <?=trlVps('Payment')?>: <?=$this->order->payment?>

    <table>
        <tr>
            <th><?=trlVps('Product')?></th>
            <th><?=trlVps('Unit Price')?></th>
            <th><?=trlVps('Amount')?></th>
            <th><?=trlVps('Price')?></th>
        </tr>
        <? foreach ($this->orderProducts as $op) { ?>
        <? $p = $op->getParentRow('Product') ?>
        <tr>
            <td><?=$p?></td>
            <td><?=$this->money($p->price)?></td>
            <td><?=$op->amount?></td>
            <td><?=$this->money($p->price * $op->amount)?></td>
        </tr>
        <? } ?>
        <tr>
            <td colspan="3"><?=trlVps('Subtotal')?>:</td>
            <td><?=$this->money($this->order->getSubTotal())?></td>
        </tr>
        <tr>
            <td colspan="3"><?=trlVps('Shipping and Handling')?>:</td>
            <td><?=$this->money($this->order->getShipping())?></td>
        </tr>
        <tr>
            <td colspan="3"><?=trlVps('Total Amount')?>:</td>
            <td><?=$this->money($this->order->getTotal())?></td>
        </tr>
    </table>

    <?=$this->componentLink($this->confirm)?>
</div>