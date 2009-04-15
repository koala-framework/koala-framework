<div class="<?=$this->cssClass?>">

    <input type="hidden" class="options" value="<?= str_replace("\"", "'", Zend_Json::encode($this->options)) ?>" />

    <? /* height wird benötigt wenn gmap innerhalb von switchDisplay liegt*/ ?>
    <div class="container" style="height: <?= $this->height; ?>px;"></div>

</div>
