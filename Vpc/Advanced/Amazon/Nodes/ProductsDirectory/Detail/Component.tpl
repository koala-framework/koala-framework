<div class="<?=$this->cssClass?>">
    <div class="bookInfos">
        <? if ($this->item->MediumImage) { ?>
            <img class="cover" src="<?=$this->item->MediumImage->Url->__toString()?>" width="<?=$this->item->MediumImage->Width?>" height="<?=$this->item->MediumImage->Height?>" alt="" />
        <? } ?>
        <h1><?=$this->product->title?></h1>
        <h2><?=$this->product->author?></h2>
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
        <div class="clear"></div>
    </div>

    <? if (isset($this->item->EditorialReviews)) { ?>
    <ul class="editorialReviews">
        <li><h2><?=trlVps('product description');?></h2></li>
        <? foreach ($this->item->EditorialReviews as $r) { ?>
            <li>
                <?=$r->Source?>
                <?=$r->Content?>
            </li>
        <? } ?>
    </ul>
    <? } ?>

    <? if (isset($this->item->CustomerReviews)) { ?>
    <ul class="customerReviews">
        <li><h2><?=trlVps('customer reviews');?></h2></li>
        <? foreach ($this->item->CustomerReviews as $r) { ?>
            <li>
                <? for($i=0; $i<$r->Rating; $i++) { ?>
                    <?=$this->image('/assets/vps/images/rating/ratingStarFull.jpg','StarFull', 'ratingStar');?>
                <? } ?>
                <? for($i=0; $i<5-$r->Rating; $i++) { ?>
                    <?=$this->image('/assets/vps/images/rating/ratingStarEmpty.jpg','StarEmpty', 'ratingStar');?>
                <? } ?>
                <span class="summary"><?=$r->Summary?></span><br/>
                <?=$r->Content?>
            </li>
        <? } ?>
    </ul>
    <? } ?>

    <? if ($this->similarProducts) { ?>
    <ul class="similarProducts">
        <li><h2><?=trlVps('similar products');?></h2></li>
        <? foreach ($this->similarProducts as $p) { ?>
            <li><?=$this->componentLink($p)?></li>
        <? } ?>
    </ul>
    <? } ?>


    <ul class="similarProducts">
        <li><h2><?=trlVps('This entry is classified in:');?></h2></li>
        <? foreach ($this->nodes as $n) { ?>
            <li><?=$this->componentLink($n)?></li>
        <? } ?>
    </ul>

    <? foreach($this->keys as $k) { ?>
        <?=$this->component($this->$k)?>
    <? } ?>

</div>


