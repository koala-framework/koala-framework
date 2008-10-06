<div class="<?=$this->cssClass?>">
    <p>
        <?=$this->orderData->firstname?> <?=$this->orderData->lastname?><br />
        <?=$this->orderData->street?><br />
        <?=$this->orderData->zip?> <?=$this->orderData->city?><br />
        <?=$this->orderData->country?>
    </p>

    <p>
        <?=$this->orderData->email?><br />
        <?=$this->orderData->phone?>
    </p>

    <?=trlVps('Payment')?>: <?=$this->orderData->payment?>

    <table>
        <tr>
            <th><?=trlVps('Product')?></th>
            <th><?=trlVps('Unit Price')?></th>
            <th><?=trlVps('Amount')?></th>
            <th><?=trlVps('Price')?></th>
        </tr>
        <? foreach ($this->orderProducts as $op) { ?>
        <? $p = $op->findParentRow('Vpc_Shop_Products') ?>
        <tr>
            <td><?=$p?></td>
            <td><?=$this->money($p->price)?></td>
            <td><?=$op->amount?></td>
            <td><?=$this->money($p->price * $op->amount)?></td>
        </tr>
        <? } ?>
        <? if ($this->shipping) { ?>
        <tr>
            <td colspan="3"><?=trlVps('Subtotal')?>:</td>
            <td><?=$this->money($this->subtotal)?></td>
        </tr>
        <tr>
            <td colspan="3"><?=trlVps('Shipping and Handling')?>:</td>
            <td><?=$this->money($this->shipping)?></td>
        </tr>
        <? } ?>
        <tr>
            <td colspan="3"><?=trlVps('Total Amount')?>:</td>
            <td><?=$this->money($this->total)?></td>
        </tr>
    </table>

    <?=$this->componentLink($this->confirm)?>
</div>