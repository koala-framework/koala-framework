<div class="<?=$this->cssClass?>">
    <h2><?= trlVps('Do you really want to delete this post?') ?></h2>
    <?= $this->component($this->data->parent->parent); ?>
    <div class="deletingPost"><?= $this->componentLink($this->data->parent->parent->parent, trlVps('No')) ?></div>
    <div class="deletingPost yes"><?= $this->componentLink($this->data->getChildComponent('_confirmed'), trlVps('Yes')) ?></div>
</div>