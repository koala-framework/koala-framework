<div class="<?=$this->cssClass;?>">
    <?= $this->component($this->data->getChildComponent('_mail')); ?>
    <?= $this->componentLink($this->data->parent->getPage(), '&laquo '.$this->data->trlKwf('Back'))?>
</div>
