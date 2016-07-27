<?php if ($this->searchForm) { ?>
<div class="<?=$this->rootElementClass?>">
    <input type="hidden" value="<?=htmlspecialchars(json_encode($this->config))?>" />
    <?=$this->component($this->searchForm)?>
</div>
<?php } ?>
