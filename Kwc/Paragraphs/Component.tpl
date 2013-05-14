<div class="<?=$this->cssClass?>">
    <? foreach ($this->paragraphs as $paragraph) { ?>
        <div class="<?=$paragraph['class'];?>" style="clear:both">
            <?=$this->component($paragraph['data']);?>
        </div>
    <? } ?>
</div>
