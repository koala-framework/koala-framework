<div class="<?=$this->cssClass;?>" data-width="100%">
    <? foreach ($this->listItems as $child) { ?>
    <div class="<?=$child['class'];?>" style="<?=$child['style'];?>">
        <?=$this->component($child['data']);?>
    </div>
    <? } ?>
    <div class="clear"></div>
</div>
