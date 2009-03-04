<div class="<?=$this->cssClass?> vpsMarqueeElements">
    <input type="hidden" class="settings" value="<?=str_replace("\"", "'",Zend_Json::encode($this->settings))?>" />

    <? foreach($this->keys as $k) { ?>
        <?=$this->component($this->$k)?>
    <? } ?>
</div>
