<div class="<?=$this->rootElementClass?> kwfMarqueeElements">
    <input type="hidden" class="settings" value="<?=str_replace("\"", "'",Zend_Json::encode($this->settings))?>" />

    <?php foreach ($this->keys as $k) { ?>
        <?=$this->component($this->$k)?>
    <?php } ?>
</div>
