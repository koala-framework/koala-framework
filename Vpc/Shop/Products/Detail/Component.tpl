<div class="<?=$this->cssClass?>">
    <?=$this->component($this->content)?>
    <p><?=trlVps('Price')?>: <?=$this->money($this->data->row->price)?></p>
    <?=$this->component($this->addToCart)?>
</div>