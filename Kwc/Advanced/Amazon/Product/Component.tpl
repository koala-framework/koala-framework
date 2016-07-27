<div class="<?=$this->rootElementClass?>">
    <?php if ($this->product) { ?>
    <?php if ($this->product->item->MediumImage) { ?>
        <img class="cover" src="<?=$this->product->item->MediumImage->Url->__toString()?>" width="<?=$this->product->item->MediumImage->Width?>" height="<?=$this->product->item->MediumImage->Height?>" alt="" />
    <?php } ?>
    <h4><?=$this->product->title?></h4>
    <h5><?=$this->product->author?></h5>
    <p><?=$this->product->formattedPrice?></p>
    <?php if(!is_null($this->product->averageRating)) { ?>
        <p><?=$this->data->trlKwf('Rating')?>:
        <?php for($i=0; $i<round($this->product->averageRating); $i++) { ?>
            <?=$this->image('/assets/kwf/images/rating/ratingStarFull.jpg','StarFull', 'ratingStar');?>
        <?php } ?>
        <?php for($i=0; $i<5-round($this->product->averageRating); $i++) { ?>
            <?=$this->image('/assets/kwf/images/rating/ratingStarEmpty.jpg','StarEmpty', 'ratingStar');?>
        <?php } ?></p>
    <?php } ?>
    <a class="order" href="<?=$this->product->detailPageURL?>" data-kwc-popup="blank"><?=$this->data->trlKwf('order now at amazon')?></a>
    <?php } ?>
</div>
