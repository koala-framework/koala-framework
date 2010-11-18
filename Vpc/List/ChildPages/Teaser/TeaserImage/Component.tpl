<div class="<?=$this->cssClass?>">
    <h2><?= $this->componentLink($this->data->targetPage); ?></h2>
    <?= $this->ifHasContent($this->image); ?>
        <div class="left prevImg">
            <?= $this->componentLink($this->data->targetPage, $this->component($this->image)); ?>
        </div>
    <?= $this->ifHasContent(); ?>
    <div class="text<?= $this->ifHasContent($this->image); ?> withImage<?= $this->ifHasContent(); ?>">
        <?= $this->component($this->text); ?>
    </div>
    <div class="clear"></div>
    <p class="goTo"><?= $this->componentLink($this->data->targetPage, $this->readMoreLinktext); ?></p>
</div>
