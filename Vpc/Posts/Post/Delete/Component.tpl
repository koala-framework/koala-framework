<div class="<?=$this->cssClass?>">
    <?= trlVps('Do you really want to delete this post?') ?>
    <?= $this->componentLink($this->data->parent->parent, trlVps('No')) ?>
    <?= $this->componentLink($this->data->getChildComponent('_confirmed'), trlVps('Yes')) ?>
    <?= $this->component($this->data->parent); ?>
</div>