<div class="<?=$this->rootElementClass?>">
    <input type="hidden" value="<?=htmlspecialchars(Zend_Json::encode($this->options))?>" />
    <?=$this->paypalButton?>
    <div class="process"></div>
</div>