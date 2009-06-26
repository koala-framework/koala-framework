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
        <?=trlVps('You pay by')?> <?=$this->paymentTypeText?>
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

        <? foreach ($this->sumRows as $row) { ?>
            <tr<? if(isset($row['class'])) {?> class="<?=$row['class']?>"<? } ?>>
                <td colspan="3"><?=$row['text']?></td>
                <td colspan="2" class="price"><?=trlVps('EUR')?> <?=$this->money($row['amount'],'')?></td>
            </tr>
        <? } ?>

    </table>
    <?=$this->component($this->confirmLink)?>
</div>