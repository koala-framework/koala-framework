<div class="<?=$this->cssClass?>">
    <div class="lightboxHeader">
        <?=$this->componentLink($this->data->parent, '&nbsp', 'closeButton')?>
    </div>
    <div class="lightboxBody">
        <? if($this->previous) { ?>
        <div class="prevBtn">
            <?=$this->componentLink($this->previous, '&nbsp')?>
        </div>
        <? } else { ?>
        <div class="prevBtnInactive"></div>
        <? } ?>

        <? if($this->next) { ?>
        <div class="nextBtn">
            <?=$this->componentLink($this->next, '&nbsp')?>
        </div>
        <? } else { ?>
        <div class="nextBtnInactive"></div>
        <? } ?>
        <div class="image" style="width:<?=$this->width?>px; height:<?=$this->height?>px">
            <img class="centerImage" src="<?=$this->imageUrl?>" width="<?=$this->width?>" height="<?=$this->height?>" alt="" />
        </div>
    </div>
    <div class="lightboxFooter">
        <? if($this->options->title) { ?>
            <p class="imageCaption<? if($this->options->title) { ?>Title<? } ?>">
                <strong><?=$this->options->imageCaption?></strong>
            </p>
        <? } ?>
        <? if($this->options->title) { ?><p class="title"><?=$this->options->title?></p><? } ?>
        <? if(isset($this->options->fullSizeUrl)) { ?>
            <p class="fullSizeLink">
                <a href="<?=$this->options->fullSizeUrl?>" class="fullSizeLink"><?=$this->data->trlVps('Download original image')?></a>
            </p>
        <? } ?>
    </div>
</div>
