<div class="<?=$this->rootElementClass?>">
    <div class="kwfUp-lightboxHeader">
    </div>
    <div class="kwfUp-lightboxBody<?php if($this->previous || $this->next) { ?> kwfUp-hasNextOrPrevious<?php } ?>">
        <?php if($this->previous) { ?>
        <div class="kwfUp-prevBtn">
            <?=$this->componentLink($this->previous, '&nbsp;',  'kwfUp-preload')?>
        </div>
        <?php } else { ?>
        <div class="kwfUp-prevBtnInactive"></div>
        <?php } ?>

        <?php if($this->next) { ?>
        <div class="kwfUp-nextBtn">
            <?=$this->componentLink($this->next, '&nbsp;',  'kwfUp-preload')?>
        </div>
        <?php } else { ?>
        <div class="kwfUp-nextBtnInactive"></div>
        <?php } ?>
        <?php if ($this->baseUrl) { ?>
        <div class="kwfUp-image" style="max-width:<?=$this->width;?>px;">
            <div class="kwfUp-container" style="padding-bottom:<?=$this->aspectRatio;?>%;"
                    data-width-steps="{{ json_encode($this->widthSteps) }}"
                    data-src="<?=$this->baseUrl;?>">
                <noscript>
                    <img class="kwfUp-centerImage kwfUp-hideWhileLoading" src="<?=$this->imageUrl?>" width="<?=$this->width?>" height="<?=$this->height?>" alt="" />
                </noscript>
            </div>
        </div>
        <?php } ?>
    </div>
    <div class="kwfUp-lightboxFooter">
        <?php if(isset($this->options->title) && $this->options->title) { ?><p class="kwfUp-title"><?=$this->options->title?></p><?php } ?>
        <?php if(isset($this->options->fullSizeUrl)) { ?>
            <p class="kwfUp-fullSizeLink">
                <a href="<?=$this->options->fullSizeUrl?>" class="kwfUp-fullSizeLink"><?=$this->data->trlKwf('Download original image')?></a>
            </p>
        <?php } ?>
    </div>
</div>
