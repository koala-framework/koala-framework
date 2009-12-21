<div class="<?=$this->cssClass?>">
    <? if (isset($this->searchForm)) echo $this->component($this->searchForm); ?>

    <input type="hidden" class="options" value="<?= str_replace("\"", "'", Zend_Json::encode($this->options)) ?>" />

    <? /* height wird benötigt wenn gmap innerhalb von switchDisplay liegt*/ ?>
    <div class="container" style="height: <?= $this->height; ?>px;"></div>

</div>
