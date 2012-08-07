<div class="<?=$this->cssClass?>">
    <input type="hidden" name="config" value="<?=htmlspecialchars(json_encode($this->config))?>" />
    <? if (isset($this->searchForm)) { ?>
        <div class="searchForm">
            <?=$this->component($this->searchForm)?>
        </div>
    <? } ?>
    <div class="viewContainer"></div>
    <div class="clear"></div>
</div>