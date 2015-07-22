<div class="<?=$this->rootElementClass?>">
    <?= $this->component($this->data->getChildComponent('_mail')); ?>
    <?= $this->componentLink($this->data->parent->getPage(), '&laquo '.$this->data->trlKwf('Back'))?>
</div>
