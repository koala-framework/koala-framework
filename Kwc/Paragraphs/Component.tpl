<div class="<?=$this->cssClass?>">
    <? foreach ($this->paragraphs as $paragraph) { ?>
        <div class="<?=$paragraph['cssClass'];?>" style="clear:both">
            <?=$this->component($paragraph['component']);?>
        </div>
    <? } ?>
</div>
