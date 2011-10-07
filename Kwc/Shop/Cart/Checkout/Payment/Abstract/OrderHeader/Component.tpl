<div class="<?=$this->cssClass?>">
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
    <? if ($this->paymentTypeText) { ?>
    <div class="orderInfo">
        <p>
            <?=trlVps('You pay by')?> <strong><?=$this->paymentTypeText?></strong>.
        </p>
    </div>
    <? } ?>
</div>