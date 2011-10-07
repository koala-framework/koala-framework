<div class="<?=$this->cssClass?>">
    <? foreach($this->keys as $k) { ?>
        <?=$this->component($this->$k)?>
    <? } ?>
    <div class="clear"></div>
</div>
