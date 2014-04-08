<div class="<?=$this->cssClass?>" data-width="100%">
    <? foreach ($this->paragraphs as $paragraph) { ?>
        <div class="<?=$paragraph['class'];?>" style="clear:both" data-width="100%">
            <?=$this->component($paragraph['data']);?>
        </div>
    <? } ?>
</div>
