<div class="<?=$this->cssClass?>">
    <input type="hidden" name="config" value="<?=htmlspecialchars(json_encode($this->config))?>" />
    <h1><?=$this->data->trl('Suche')?></h1>
    <? if (isset($this->searchForm)) { ?>
        <div class="searchForm">
            <?=$this->component($this->searchForm)?>
        </div>
    <? } ?>
    <h2><?=$this->data->trl('Suchergebnisse')?></h2>
    <div class="viewContainer">
        <?=$this->partials($this->data);?>
    </div>
    <div class="clear"></div>
    <? if (isset($this->paging)) echo $this->component($this->paging); ?>
</div>