<div class="<?=$this->cssClass?>">
    <div class="cartList">
        <h2><?=trlVps('Cart')?></h2>
        <? if (!$this->items) { ?>
            <p><?=trlVps('Cart is empty')?></p>
    </div>
        <? } else { ?>
            <div class="countProducts">
                <p>
                    <?=trlVps('You cart contains {0} products','<strong>'.$this->order->getTotalAmount().'</strong>')?>
                </p>
            </div>
            <table class="tblBoxCart" cellspacing="0" cellpadding="0">
            <?
            $c=0;
            foreach ($this->items as $i) { ?>
                <tr class="products<?=($c%2==0 ? ' row1' : ' row2');?>">
                    <td class="amount"><?=$i->row->amount?>x</td>
                    <td class="product"><?=$this->componentLink($i->product)?></td>
                    <td class="price"><?=trlVps('EUR')?> <?=$this->money($i->product->row->price*$i->row->amount, '')?></td>
                </tr>
                <? $c++;
            } ?>
            </table>
            <ul class="moneyInfo webListNone">
                <? foreach ($this->sumRows as $row) { ?>
                    <li<? if(isset($row['class'])) {?> class="<?=$row['class']?>"<? } ?>>
                        <span class="text"><?=$row['text']?></span>
                        <span class="price"><?=trlVps('EUR')?> <?=$this->money($row['amount'],'')?></span>
                        <div class="clear"></div>
                    </li>
                <? } ?>
            </ul>
        </div>
        <div class="cartOrder">
            <div class="cart">
                <?=$this->componentLink($this->cart, $this->placeholder['toCart'])?>
            </div>
            <div class="checkout">
                <?=$this->componentLink($this->checkout, $this->placeholder['toCheckout'])?>
            </div>
        </div>
    <? } ?>
</div>