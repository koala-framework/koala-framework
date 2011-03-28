<div class="<?=$this->cssClass?>">
    <? if ($this->product) { ?>
    <? if ($this->product->getItem()->MediumImage) { ?>
        <img class="cover" src="<?=$this->product->getItem()->MediumImage->Url->__toString()?>" width="<?=$this->product->getItem()->MediumImage->Width?>" height="<?=$this->product->getItem()->MediumImage->Height?>" alt="" />
    <? } ?>
    <h4><?=$this->product->title?></h4>
    <h5><?=$this->product->author?></h5>
    <p><?=$this->product->formattedPrice?></p>
    <? if(!is_null($this->product->averageRating)) { ?>
        <p><?=trlVps('Rating')?>:
        <? for($i=0; $i<round($this->product->averageRating); $i++) { ?>
            <?=$this->image('/assets/vps/images/rating/ratingStarFull.jpg','StarFull', 'ratingStar');?>
        <? } ?>
        <? for($i=0; $i<5-round($this->product->averageRating); $i++) { ?>
            <?=$this->image('/assets/vps/images/rating/ratingStarEmpty.jpg','StarEmpty', 'ratingStar');?>
        <? } ?></p>
    <? } ?>
    <a class="order" href="<?=$this->product->detailPageURL?>" rel="popup_blank"><?=trlVps('order now at amazon')?></a>
    <? } ?>
</div>
