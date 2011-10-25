<div class="<?=$this->cssClass?>">
    <?=$this->component($this->form);?>
    <div class="back"><?=$this->componentLink($this->data->getParentPage(), $this->placeholder['backToCart']);?></div>
</div>