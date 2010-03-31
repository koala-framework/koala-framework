<? if ($this->data->hasContent()) { ?>
    <div class="<?=$this->cssClass?> vpcAbstractFlash">
        <div class="flashWrapper"><a href="http://get.adobe.com/de/flashplayer/">Get the Flash Player</a> to see this player.</div>
        <input type="hidden" class="flashData" value="<?= htmlspecialchars(Zend_Json::encode($this->flash)) ?>" />
    </div>
<? } ?>