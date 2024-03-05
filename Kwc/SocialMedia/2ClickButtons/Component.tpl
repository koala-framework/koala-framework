<div class="<?=$this->rootElementClass?>">
    <input type="hidden" class="config" value="<?= Kwf_Util_HtmlSpecialChars::filter(Zend_Json::encode($this->config)) ?>" />
    <div class="socialShareButtons"></div>
</div>
