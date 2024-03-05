<div class="<?=$this->rootElementClass?>">
    <div class="content">
        <input type="hidden" class="options" value="<?=Kwf_Util_HtmlSpecialChars::filter(Zend_Json::encode($this->options))?>" />
    </div>
</div>
