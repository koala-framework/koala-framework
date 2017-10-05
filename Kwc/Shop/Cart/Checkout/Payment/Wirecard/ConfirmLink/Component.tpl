<div class="<?=$this->rootElementClass?>">
    <input type="hidden" value="<?=Kwf_Util_HtmlSpecialChars::filter(Zend_Json::encode($this->options))?>" />
    <?=$this->wirecardButton?>
    <div class="process"></div>
</div>
