<?php if ($this->searchForm) { ?>
<div class="<?=$this->rootElementClass?>">
    <input type="hidden" value="<?=Kwf_Util_HtmlSpecialChars::filter(json_encode($this->config))?>" />
    <?=$this->component($this->searchForm)?>
</div>
<?php } ?>
