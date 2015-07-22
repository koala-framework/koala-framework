<div class="<?=$this->rootElementClass?>">
    <?=$this->component($this->content)?>
    <div class="orderStuff">
        <div class="background">
            <div class="price"><p><?=$this->money($this->currentPrice,'')?></p></div>
            <?=$this->component($this->addToCart)?>
        </div>
        <div class="back"><a href="<?=$this->data->getParentPage()->url?>" onclick="history.back();return false;"><?=$this->placeholder['back']?></a></div>
    </div>
</div>