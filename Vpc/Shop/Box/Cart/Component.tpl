<div class="<?=$this->cssClass?>">
    <? if (!$this->items) { ?>
    <?=trlVps('Cart is empty')?>
    <? } else { ?>
    <? foreach ($this->items as $i) { ?>
        <?=$this->componentLink($i->product)?>
        <?=trlVps('Size')?>: <?=$i->row->size?>
        <?=trlVps('Amount')?>: <?=$i->row->amount?>
        <?=trlVps('Price')?>: <?=$this->money($i->product->row->price*$i->row->amount)?>
    <? } ?>
    <?=$this->componentLink($this->cart)?>
    <?=$this->componentLink($this->checkout)?>
    <? } ?>
</div>