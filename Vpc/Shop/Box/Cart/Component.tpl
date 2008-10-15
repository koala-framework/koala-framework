<div class="<?=$this->cssClass?>">
    <div class="cartList">
        <h2><?=trlVps('Cart')?></h2>
        <? if (!$this->items) { ?>
            <p><?=trlVps('Cart is empty')?></p>
        <? } else { ?>
        <div class="countProducts"><?=trlVps('You cart contains {0} products','<strong>'.count($this->items).'</strong>')?></div>
        <? foreach ($this->items as $i) { ?>
            <div class="cartProduct">
                <div class="cartName"><?=$this->componentLink($i->product)?></div>
                <div class="cartSize"><?=trlVps('Size')?>: <?=$i->row->size?></div>
                <div class="cartAmount"><?=trlVps('Amount')?>: <?=$i->row->amount?></div>
                <div class="cartPrice"><?=trlVps('Price')?>: <?=trlVps('EUR')?> <?=$this->money($i->product->row->price*$i->row->amount, '')?></div>
                <div class="clear"></div>
            </div>
        <? } ?>
        <div class="moneyInfo">
            <?=trlVps('Shipping and Handling')?>: <?=trlVps('EUR')?> <?=$this->money($this->order->getShipping(),'')?><br />
            <?=trlVps('Total Amount')?>: <strong><?=trlVps('EUR')?> <?=$this->money($this->order->getTotal(),'')?></strong>
        </div>
    </div>
    <div class="cartOrder">
        <div class="cart"><?=$this->componentLink($this->cart, trlVps('To cart'))?></div>
        <div class="checkout"><?=$this->componentLink($this->checkout, trlVps('To checkout'))?></div>
    </div>
    <? } ?>
</div>