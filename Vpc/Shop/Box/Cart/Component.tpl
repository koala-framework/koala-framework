<div class="<?=$this->cssClass?>">
    <? if (!$this->items) { ?>
    <?=trlVps('Cart is empty')?>
    <? } else { ?>
    <? foreach ($this->items as $i) { ?>
        <?=$i->row->amount?>
        <?=$this->componentLink($i->product)?>
    <? } ?>
    <?=$this->componentLink($this->cart)?>
    <?=$this->componentLink($this->checkout)?>
    <? } ?>
</div>