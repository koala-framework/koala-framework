<div class="<?=$this->cssClass?><? if (!$this->optimizedMobileUI) { ?> noOtimizedMobileUI<? } ?>">
    <div class="mobileOverlay">
        <img class="navigateIcon" src="/assets/kwf/Kwc/Directories/List/ViewMap/navigateIcon.png" />
        <div class="innerMobileOverlay">
            <span class="tapToNav"><?=$this->data->trlKwf('Tap to navigate');?></span>
            <span class="tapToScroll"><?=$this->data->trlKwf('close');?></span>
        </div>
    </div>

    <? if (isset($this->searchForm)) echo $this->component($this->searchForm); ?>

    <input type="hidden" class="options" value="<?= htmlspecialchars(Zend_Json::encode($this->options)) ?>" />

    <?=$this->partials($this->data);?>

    <? /* height wird benötigt wenn gmap innerhalb von switchDisplay liegt*/ ?>
    <div class="container" style="height: <?= $this->height; ?>px;"></div>

</div>
