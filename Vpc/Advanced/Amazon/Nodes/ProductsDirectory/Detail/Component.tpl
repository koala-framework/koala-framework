<div class="<?=$this->cssClass?>">
    <? if ($this->item->SmallImage) { ?>
    <img src="<?=$this->item->SmallImage->Url->__toString()?>" width="<?=$this->item->SmallImage->Width?>" height="<?=$this->item->SmallImage->Height?>" alt="" />
    <? } ?>
    <h1><?=$this->product->title?></h1>
    <h2><?=$this->product->author?></h2>
    <p><?=$this->product->formattedPrice?></p>
    <? if(!is_null($this->product->averageRating)) { ?>
    <p><?=trlVps('Rating')?>: <?=$this->product->averageRating?></p>
    <? } ?>
    <a href="<?=$this->product->detailPageURL?>" rel="popup_blank">bestellen</a>

    <? if (isset($this->item->EditorialReviews)) { ?>
    <ul>
    <? foreach ($this->item->EditorialReviews as $r) { ?>
        <li>
        <?=$r->Source?>
        <?=$r->Content?>
        </li>
    <? } ?>
    </ul>
    <? } ?>

    <? if (isset($this->item->CustomerReviews)) { ?>
    <ul>
    <? foreach ($this->item->CustomerReviews as $r) { ?>
        <li>
        <?=$r->Rating?>
        <?=$r->HelpfulVotes?>/<?=$r->TotalVotes?>
        <?=$this->date($r->Date)?>
        <?=$r->Summary?>
        <?=$r->Content?>
        </li>
    <? } ?>
    </ul>
    <? } ?>

    <? if ($this->similarProducts) { ?>
    <ul>
    <? foreach ($this->similarProducts as $p) { ?>
        <li><?=$this->componentLink($p)?></li>
    <? } ?>
    </ul>
    <? } ?>

</div>


