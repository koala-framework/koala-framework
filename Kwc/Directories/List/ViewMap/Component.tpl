<div class="<?=$this->rootElementClass?>">
    <? if (isset($this->searchForm)) echo $this->component($this->searchForm); ?>

    <input type="hidden" class="options" value="<?= htmlspecialchars(Zend_Json::encode($this->options)) ?>" />

    <? /* height wird benötigt wenn gmap innerhalb von switchDisplay liegt*/ ?>
    <div class="container" style="height: <?= $this->height; ?>px;"></div>

</div>
