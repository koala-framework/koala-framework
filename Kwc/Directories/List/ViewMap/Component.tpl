<div class="<?=$this->rootElementClass?><? if ($this->optimizedMobileUI) { ?> optimizedMobileUI<? } ?>">
    <div class="mobileOverlayOpen">
        <div class="innerMobileOverlay">
            <span class="tapToNav"><?=$this->data->trlKwf('Tap to navigate');?></span>
        </div>
    </div>

    <div class="mobileOverlayClose">
        <div class="innerMobileOverlay">
            <span class="tapToScroll"><?=$this->data->trlKwf('Close');?></span>
        </div>
    </div>

    <? if (isset($this->searchForm)) echo $this->component($this->searchForm); ?>

    <input type="hidden" class="options" value="<?= htmlspecialchars(Zend_Json::encode($this->options)) ?>" />

    <? /* height wird benötigt wenn gmap innerhalb von switchDisplay liegt*/ ?>
    <div class="container" style="height: <?= $this->height; ?>px;"></div>

</div>
