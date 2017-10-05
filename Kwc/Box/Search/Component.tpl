<div class="<?=$this->rootElementClass?>">
    <form action="<?= $this->searchPageUrl; ?>" method="get" autocomplete="off">
        <input type="hidden" class="ajaxUrl" value="<?= $this->ajaxUrl; ?>" />
        <input type="hidden" class="searchSettings" value="<?= Kwf_Util_HtmlSpecialChars::filter(Zend_Json::encode($this->searchSettings)) ?>" />
        <input type="text" name="<?=$this->queryParam?>" class="searchField kwfUp-kwfClearOnFocus"
            value="<?= $this->placeholder['clearOnFocus']; ?>" autocomplete="off" />
        <input type="hidden" class="submitParam" name="<?=$this->submitParam?>" value="submit" autocomplete="off" />
        <button type="submit" class="submit"><?= $this->placeholder['searchButton']; ?></button>
    </form>
    <div class="searchResult">
        <div class="initialResultText">
            <?= $this->placeholder['initialResultText']; ?>
        </div>
    </div>
</div>
