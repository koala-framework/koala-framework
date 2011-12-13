<div class="<?=$this->cssClass?>">
    <div class="lightboxHeader">
        <?=$this->componentLink($this->data->parent, '&nbsp;', 'closeButton')?>
    </div>
    <div class="lightboxBody">
        <? if($this->previous) { ?>
        <div class="prevBtn">
            <?=$this->componentLink($this->previous, '&nbsp;',  'preload')?>
        </div>
        <? } else { ?>
        <div class="prevBtnInactive"></div>
        <? } ?>

        <? if($this->next) { ?>
        <div class="nextBtn">
            <?=$this->componentLink($this->next, '&nbsp;',  'preload')?>
        </div>
        <? } else { ?>
        <div class="nextBtnInactive"></div>
        <? } ?>
        <div class="image" style="width:<?=$this->width?>px; height:<?=$this->height?>px">
            <img class="centerImage hideWhileLoading" src="<?=$this->imageUrl?>" width="<?=$this->width?>" height="<?=$this->height?>" alt="" />
        </div>
    </div>
    <div class="lightboxFooter">
        <? if(isset($this->options->imageCaption) && $this->options->imageCaption) { ?>
            <p class="imageCaption<? if(isset($this->options->title) && $this->options->title) { ?>Title<? } ?>">
                <strong><?=$this->options->imageCaption?></strong>
            </p>
        <? } ?>
        <? if(isset($this->options->title) && $this->options->title) { ?><p class="title"><?=$this->options->title?></p><? } ?>
        <? if(isset($this->options->fullSizeUrl)) { ?>
            <p class="fullSizeLink">
                <a href="<?=$this->options->fullSizeUrl?>" class="fullSizeLink"><?=$this->data->trlKwf('Download original image')?></a>
            </p>
        <? } ?>
    </div>
</div>
