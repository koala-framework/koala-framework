<div class="<?=$this->cssClass?>">
    <h2><?= $this->componentLink($this->data->targetPage); ?></h2>
    <? if ($this->hasContent($this->image)) { ?>
        <div class="left prevImg">
            <?= $this->componentLink($this->data->targetPage, $this->component($this->image)); ?>
        </div>
    <? } ?>
    <div class="text<?if ($this->hasContent($this->image)) { ?> withImage<?}?>">
        <?= $this->component($this->text); ?>
    </div>
    <div class="clear"></div>
    <p class="goTo"><?= $this->componentLink($this->data->targetPage, $this->readMoreLinktext); ?></p>
</div>
