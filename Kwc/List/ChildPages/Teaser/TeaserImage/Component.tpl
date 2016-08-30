<div class="<?=$this->rootElementClass?>">
    <h2><?= $this->componentLink($this->data->targetPage, null, $this->headlineComponentLinkConfig); ?></h2>
    <?php if ($this->hasContent($this->image)) { ?>
        <div class="left prevImg">
            <?= $this->componentLink($this->data->targetPage, $this->component($this->image)); ?>
        </div>
    <?php } ?>
    <div class="text<?php if ($this->hasContent($this->image)) { ?> withImage<?php } ?>">
        <?= $this->component($this->text); ?>
    </div>
    <div class="kwfUp-clear"></div>
    <p class="goTo"><?= $this->componentLink($this->data->targetPage, $this->readMoreLinktext, $this->readMoreComponentLinkConfig); ?></p>
</div>
