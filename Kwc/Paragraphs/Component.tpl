<div class="<?=$this->rootElementClass?>" data-width="100%">
    <? foreach ($this->paragraphs as $paragraph) { ?>
        <?=$paragraph['preHtml']?>
        <div class="<?=$paragraph['class'];?>" data-width="100%">
            <?=$this->component($paragraph['data']);?>
        </div>
        <?=$paragraph['postHtml']?>
    <? } ?>
</div>
