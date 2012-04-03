<div class="<?=$this->cssClass;?>">
    <? foreach ($this->listItems as $child) { ?>
    <div class="<?=$child['class'];?>" class="<?=$child['style'];?>">
        <?=$this->component($child['data']);?>
    </div>
    <? } ?>
    <div class="clear"></div>
</div>