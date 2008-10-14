<div class="<?=$this->cssClass?>">
    <? if (!$this->items) { ?>
        <div class="cartProduct">
            <?=trlVps('Cart is empty')?>
        </div>
    <? } else { ?>
    <? foreach ($this->items as $i) { ?>
        <div class="cartProduct">
            <div class="cartName"><?=$this->componentLink($i->product)?></div>
            <div class="cartSize"><?=trlVps('Size')?>: <?=$i->row->size?></div>
            <div class="cartAmount"><?=trlVps('Amount')?>: <?=$i->row->amount?></div>
            <div class="cartPrice"><?=trlVps('Price')?>: <?=$this->money($i->product->row->price*$i->row->amount)?></div>
        </div>
    <? } ?>
    <div class="cart"><?=$this->componentLink($this->cart)?></div>
    <div class="checkout"><?=$this->componentLink($this->checkout)?></div>
    <? } ?>
</div>