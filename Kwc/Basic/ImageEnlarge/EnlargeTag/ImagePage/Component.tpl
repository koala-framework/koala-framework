<div class="<?=$this->rootElementClass?>">
    <div class="lightboxHeader">
    </div>
    <div class="lightboxBody<? if($this->previous || $this->next) { ?> hasNextOrPrevious<? } ?>">
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
        <? if ($this->baseUrl) { ?>
        <div class="image" style="max-width:<?=$this->width;?>px;">
            <div class="container" style="padding-bottom:<?=$this->aspectRatio;?>%;"
                    data-min-width="<?=$this->minWidth;?>"
                    data-max-width="<?=$this->maxWidth;?>"
                    data-src="<?=$this->baseUrl;?>">
                <noscript>
                    <img class="centerImage hideWhileLoading" src="<?=$this->imageUrl?>" width="<?=$this->width?>" height="<?=$this->height?>" alt="" />
                </noscript>
            </div>
        </div>
        <? } ?>
    </div>
    <div class="lightboxFooter">
        <? if(isset($this->options->title) && $this->options->title) { ?><p class="title"><?=$this->options->title?></p><? } ?>
        <? if(isset($this->options->fullSizeUrl)) { ?>
            <p class="fullSizeLink">
                <a href="<?=$this->options->fullSizeUrl?>" class="fullSizeLink"><?=$this->data->trlKwf('Download original image')?></a>
            </p>
        <? } ?>
    </div>
</div>
