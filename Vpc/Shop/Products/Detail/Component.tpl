<div class="<?=$this->cssClass?>">
    <?=$this->component($this->content)?>
    <div class="orderStuff">
        <div class="background">
            <div class="price"><p><?=$this->money($this->data->row->current_price,'')?></p></div>
            <?=$this->component($this->addToCart)?>
        </div>
        <div class="back"><?=$this->componentLink($this->data->getParentPage(), $this->placeholder['backToShop']);?></div>
    </div>
</div>