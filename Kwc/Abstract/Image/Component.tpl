<div class="<?=$this->cssClass?>"
    style="max-width:<?=$this->width;?>px;<? if ($this->defineWidth) {?> width:<?=$this->width;?>px;<? } ?>"
    data-width="100%"
    data-max-width="<?=$this->maxWidth;?>">
    <? if ($this->baseUrl) { ?>
        <div class="outerContainer">
            <div class="container<? if ($this->width>100) { ?> webResponsiveImgLoading<? } ?><? if (!$this->lazyLoadOutOfViewport) {?> loadImmediately<?} ?>" style="padding-bottom:<?=$this->aspectRatio;?>%"
                data-min-width="<?=$this->minWidth;?>"
                data-max-width="<?=$this->maxWidth;?>"
                data-src="<?=$this->baseUrl;?>">
                <img />
                <noscript>
                    <?=$this->image($this->image, $this->altText, $this->imgCssClass)?>
                </noscript>
            </div>
        </div>
    <? if ($this->showImageCaption) { ?>
        <div class="imageCaption" style="max-width:<?=$this->imageParam($this->image,'width','default');?>px;"><?=(!empty($this->image_caption) ? $this->image_caption : '');?></div>
    <? } ?>
    <? } ?>
</div>
