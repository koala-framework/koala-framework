<div class="<?=$this->rootElementClass?>">
    <? foreach($this->keys as $k) { ?>
        <?=$this->component($this->$k)?>
    <? } ?>
    <div class="kwfUp-clear"></div>
</div>
