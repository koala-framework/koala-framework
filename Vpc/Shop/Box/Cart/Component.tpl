<div class="<?=$this->cssClass?>">
    <div class="cartList">
        <h2><?=trlVps('Cart')?></h2>
        <? if (!$this->items) { ?>
            <p><?=trlVps('Cart is empty')?></p>
        <? } else { ?>
        <div class="countProducts"><?=trlVps('You cart contains {0} products','<strong>'.$this->order->getTotalAmount().'</strong>')?></div>
        <? foreach ($this->items as $i) { ?>
            <div class="cartProduct">
                <div class="cartName"><?=$this->componentLink($i->product)?></div>
                <div class="cartPrice"><?=trlVps('EUR')?> <?=$this->money($i->product->row->price*$i->row->amount, '')?></div>
                <div class="cartAmount"><?=trlVps('Amount')?>: <?=$i->row->amount?></div>
                <div class="cartSize"><?=trlVps('Size')?>: <?=$i->row->size?></div>
                <div class="clear"></div>
            </div>
        <? } ?>
        <div class="moneyInfo">
            <div class="shippingText"><?=trlVps('Shipping and Handling')?>:</div><div class="shippingPrice"><?=trlVps('EUR')?> <?=$this->money($this->order->getShipping(),'')?></div><br />
            <div class="amountText"><?=trlVps('Total Amount')?>:</div><div class="amountPrice"><strong><?=trlVps('EUR')?> <?=$this->money($this->order->getTotal(),'')?></strong></div>
            <div class="clear"></div>
        </div>
    </div>
    <div class="cartOrder">
        <div class="cart"><?=$this->componentLink($this->cart, trlVps('To cart'))?></div>
        <div class="checkout"><?=$this->componentLink($this->checkout, trlVps('To checkout'))?></div>
    </div>
    <? } ?>
</div>