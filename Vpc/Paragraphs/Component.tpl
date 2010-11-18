<div class="<?=$this->cssClass?>">
    <? foreach ($this->paragraphs as $paragraph) { ?>
        <div class="vpcParagraphItem" style="clear:both">
            <?=$this->component($paragraph);?>
        </div>
    <? } ?>
</div>
