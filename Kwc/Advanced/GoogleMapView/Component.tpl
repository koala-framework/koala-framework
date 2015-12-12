<div class="<?=$this->rootElementClass?><? if ($this->optimizedMobileUI) { ?> optimizedMobileUI<? } ?>">
<? if($this->data->hasContent()) { ?>
    <div class="mobileOverlay">
        <div class="innerMobileOverlay">
            <span class="tapToNav"><?=$this->data->trlKwf('Tap to navigate');?></span>
        </div>
    </div>

    <div class="mobileOverlayClose">
        <div class="innerMobileOverlay">
            <span class="tapToScroll"><?=$this->data->trlKwf('Close');?></span>
        </div>
    </div>

    <? if($this->data->hasContent()) { ?>

        <? if ($this->text && ($this->text instanceof Kwf_Component_Data)) { ?>
            <?if ($this->hasContent($this->text)) {?>
                <div class="text">
                    <?= $this->component($this->text); ?>
                </div>
            <? } ?>
        <? } else if ($this->text) { ?>
            <div class="text">
                <?= $this->text; ?>
            </div>
        <? } ?>

        <input type="hidden" class="options" value="<?= htmlspecialchars(Zend_Json::encode($this->options)) ?>" />

        <? /* height wird benötigt wenn gmap innerhalb von switchDisplay liegt*/ ?>
        <div class="container" style="height: <?= $this->height; ?>px;"></div>

        <? if ($this->options['routing']) { ?>
            <form action="#" class="fromAddress">
                <input type="text" class="textBefore kwfUp-kwfClearOnFocus" value="<?= $this->data->trlKwf('Place of departure: zip code, Town, Street'); ?>" />
                <button class="submitOn"><?= $this->data->trlKwf('Show Route') ?></button>
                <div class="kwfUp-clear"></div>
            </form>
        <? } ?>

        <div class="mapDirSuggestParent">
            <b><?= $this->data->trlKwf('Suggestions') ?></b>
            <ul class="mapDirSuggest"></ul>
        </div>

        <div class="mapDir"></div>
    <? } else { ?>
        <?=$this->placeholder['noCoordinates']?>
    <? } ?>
</div>

