<div class="<?=$this->cssClass?>">
    <div class="cartList">
        <h2><?=trlVps('Cart')?></h2>
        <? if (!$this->items) { ?>
            <p><?=trlVps('Cart is empty')?></p>
    </div>
        <? } else { ?>
            <div class="countProducts">
                <?=trlVps('You cart contains {0} products','<strong>'.$this->order->getTotalAmount().'</strong>')?>
            </div>
            <? foreach ($this->items as $i) { ?>
                <div class="cartProduct">
                    <div class="cartName">
                        <?=$this->componentLink($i->product)?>
                    </div>
                    <div class="cartAmount">
                        <?=trlVps('Amount')?>: <?=$i->row->amount?>
                    </div>
                    <div class="cartSize">
                        <?=trlVps('Size')?>: <?=$i->row->size?>
                    </div>
                    <div class="cartPrice">
                        <?=trlVps('EUR')?> <?=$this->money($i->product->row->price*$i->row->amount, '')?>
                    </div>
                    <div class="clear"></div>
                </div>
            <? } ?>
            <ul class="moneyInfo">
                <? foreach ($this->sumRows as $row) { ?>
                    <li<? if(isset($row['class'])) {?> class="<?=$row['class']?>"<? } ?>>
                        <span class="text"><?=$row['text']?></span>
                        <span class="price"><?=trlVps('EUR')?> <?=$this->money($row['amount'],'')?></span>
                    </li>
                <? } ?>
            </ul>
        </div>
        <div class="cartOrder">
            <div class="cart">
                <?=$this->componentLink($this->cart, trlVps('To cart'))?>
            </div>
            <div class="checkout">
                <?=$this->componentLink($this->checkout, trlVps('To checkout'))?>
            </div>
        </div>
    <? } ?>
</div>