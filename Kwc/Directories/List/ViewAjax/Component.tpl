<div class="<?=$this->cssClass?>">
    <input type="hidden" name="config" value="<?=htmlspecialchars(json_encode($this->config))?>" />
    <? if (isset($this->searchForm)) { ?>
        <div class="searchForm">
            <?=$this->component($this->searchForm)?>
        </div>
    <? } ?>
    <? if (isset($this->count)) echo $this->component($this->count); ?>
    <div class="viewContainer">
        <?=$this->partials($this->data);?>
    </div>
    <div class="clear"></div>
    <? if (isset($this->paging)) echo $this->component($this->paging); ?>
</div>