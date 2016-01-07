<div class="<?=$this->rootElementClass?>">
    <div class="kwfUp-lightboxHeader">
    </div>
    <div class="kwfUp-lightboxBody<? if($this->previous || $this->next) { ?> kwfUp-hasNextOrPrevious<? } ?>">
        <? if($this->previous) { ?>
        <div class="kwfUp-prevBtn">
            <?=$this->componentLink($this->previous, '&nbsp;',  'kwfUp-preload')?>
        </div>
        <? } else { ?>
        <div class="kwfUp-prevBtnInactive"></div>
        <? } ?>

        <? if($this->next) { ?>
        <div class="kwfUp-nextBtn">
            <?=$this->componentLink($this->next, '&nbsp;',  'kwfUp-preload')?>
        </div>
        <? } else { ?>
        <div class="kwfUp-nextBtnInactive"></div>
        <? } ?>
        <? if ($this->baseUrl) { ?>
        <div class="kwfUp-image" style="max-width:<?=$this->width;?>px;">
            <div class="kwfUp-container" style="padding-bottom:<?=$this->aspectRatio;?>%;"
                    data-min-width="<?=$this->minWidth;?>"
                    data-max-width="<?=$this->maxWidth;?>"
                    data-src="<?=$this->baseUrl;?>">
                <noscript>
                    <img class="kwfUp-centerImage kwfUp-hideWhileLoading" src="<?=$this->imageUrl?>" width="<?=$this->width?>" height="<?=$this->height?>" alt="" />
                </noscript>
            </div>
        </div>
        <? } ?>
    </div>
    <div class="kwfUp-lightboxFooter">
        <? if(isset($this->options->title) && $this->options->title) { ?><p class="kwfUp-title"><?=$this->options->title?></p><? } ?>
        <? if(isset($this->options->fullSizeUrl)) { ?>
            <p class="kwfUp-fullSizeLink">
                <a href="<?=$this->options->fullSizeUrl?>" class="kwfUp-fullSizeLink"><?=$this->data->trlKwf('Download original image')?></a>
            </p>
        <? } ?>
    </div>
</div>
