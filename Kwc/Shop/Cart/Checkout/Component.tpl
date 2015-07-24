<div class="<?=$this->rootElementClass?>">
    <?=$this->component($this->form);?>
    <div class="back"><?=$this->componentLink($this->data->getParentPage(), $this->placeholder['backToCart']);?></div>
</div>