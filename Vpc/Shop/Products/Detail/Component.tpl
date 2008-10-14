<div class="<?=$this->cssClass?>">
    <?=$this->component($this->content)?>
    <div class="price"><p><?=trlVps('Price')?>: <?=$this->money($this->data->row->price)?></p></div>
    <?=$this->component($this->addToCart)?>
</div>