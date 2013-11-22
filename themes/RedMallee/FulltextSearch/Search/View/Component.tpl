<div class="<?=$this->cssClass?>">
    <input type="hidden" name="config" value="<?=htmlspecialchars(json_encode($this->config))?>" />
    <h1><?=$this->data->trl('Suchergebnisse')?></h1>
    <div class="viewContainer">
        <?=$this->partials($this->data);?>
    </div>
    <div class="clear"></div>
    <? if (isset($this->paging)) echo $this->component($this->paging); ?>
</div>
