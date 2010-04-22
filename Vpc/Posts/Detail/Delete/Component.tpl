<div class="<?=$this->cssClass?>">
    <h2><?= $this->placeholder['deletePost'] ?></h2>
    <?= $this->component($this->data->parent->parent); ?>
    <div class="deletingPost"><?= $this->componentLink($this->data->parent->parent->parent, $this->data->trlVps('No')) ?></div>
    <div class="deletingPost yes"><?= $this->componentLink($this->data->getChildComponent('_confirmed'), $this->data->trlVps('Yes')) ?></div>
</div>