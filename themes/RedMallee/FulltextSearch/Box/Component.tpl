<? if ($this->searchForm) { ?>
<div class="<?=$this->cssClass?>">
    <input type="hidden" value="<?=htmlspecialchars(json_encode($this->config))?>" />
    <?=$this->component($this->searchForm)?>
    <div class="loupe"></div>
</div>
<? } ?>
