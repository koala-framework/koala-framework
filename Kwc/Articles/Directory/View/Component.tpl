<div class="<?=$this->rootElementClass?>">
    <input type="hidden" name="config" value="<?=Kwf_Util_HtmlSpecialChars::filter(json_encode($this->config))?>" />
    <?php if (isset($this->searchForm)) { ?>
        <div class="searchForm">
            <?=$this->component($this->searchForm)?>
        </div>
    <?php } ?>
    <div class="viewContainer">
        <?=$this->partials($this->data);?>
    </div>
</div>
