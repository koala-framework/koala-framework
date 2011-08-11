<div class="<?=$this->cssClass?>">
<? if($this->data->hasContent()) { ?>

    <? if ($this->text && ($this->text instanceof Vps_Component_Data)) { ?>
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
        <form action="#" class="fromAddress printHidden">
            <input type="text" class="textBefore vpsClearOnFocus" value="<?= $this->data->trlVps('Place of departure: zip code, Town, Street'); ?>" />
            <button class="submitOn"><?= $this->data->trlVps('Show Route') ?></button>
            <div class="clear"></div>
        </form>
    <? } ?>

    <div class="mapDirSuggestParent">
        <b><?= $this->data->trlVps('Suggestions') ?></b>
        <ul class="mapDirSuggest"></ul>
    </div>

    <div class="mapDir"></div>
<? } else { ?>
    <?=$this->placeholder['noCoordinates']?>
<? } ?>
</div>

