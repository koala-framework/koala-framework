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
            $c=1;
            $j=count($this->items);
            foreach ($this->items as $i) { ?>
                <tr class="products<?=($c%2==0 ? ' row2' : ' row1');?>">
                    <td class="amount"><?=$i->row->amount?>x</td>
                    <td class="product"><?=$this->componentLink($i->product)?></td>
                    <? foreach($i->additionalOrderData as $d) { ?>
                        <td class="<?=$d['class']?>"><?=$d['name']?>: <?=$d['value']?></td>
                    <? } ?>
                    <td class="price"><?=$this->money($i->row->price*$i->row->amount, '')?></td>
                </tr>
                <tr class="<?=($c==$j ? 'lastline' : 'line');?>">
                    <td colspan="<?=(4+count($i->additionalOrderData))?>">
                        <div class="line"></div>
                    </td>
                </tr>
                <? $c++;
            } ?>
            </table>
            <ul class="moneyInfo webListNone">
                <? foreach ($this->sumRows as $row) { ?>
                    <li<? if(isset($row['class'])) {?> class="<?=$row['class']?>"<? } ?>>
                        <span class="text"><?=$row['text']?></span>
                        <span class="price"><?=$this->money($row['amount'],'')?></span>
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