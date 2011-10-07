<? if ($this->markerData) { ?>
    <input type="hidden" class="markerData" value="<?=htmlspecialchars(Zend_Json::encode(
        $this->markerData
    ));?>" />
<? } ?>
