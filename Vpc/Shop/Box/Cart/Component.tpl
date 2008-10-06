<div class="<?=$this->cssClass?>">
    <? foreach ($this->items as $i) { ?>
        <?=$i->row->amount?>
        <?=$this->componentLink($i->product)?>
    <? } ?>
    <?=$this->componentLink($this->cart)?>
    <?=$this->componentLink($this->checkout)?>
</div>