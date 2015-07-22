<div class="<?=$this->rootElementClass?>" data-width="100%">
    <? foreach ($this->listItems as $child) { ?>
    <div class="<?=$child['class'];?>" style="<?=$child['style'];?>">
        <?=$this->component($child['data']);?>
    </div>
    <? } ?>
</div>
