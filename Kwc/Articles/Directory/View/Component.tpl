<div class="<?=$this->rootElementClass?>">
    <input type="hidden" name="config" value="<?=htmlspecialchars(json_encode($this->config))?>" />
    <? if (isset($this->searchForm)) { ?>
        <div class="searchForm">
            <?=$this->component($this->searchForm)?>
        </div>
    <? } ?>
    <div class="viewContainer">
        <?=$this->partials($this->data);?>
    </div>
</div>