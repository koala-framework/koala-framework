<div class="<?=$this->cssClass?>">
    <h2><?=trlVps('Please enter your address');?>.</h2><br />
    <?=$this->component($this->form);?>
    <div class="back"><?=$this->componentLink($this->data->getParentPage(),trlVps('Back to cart'));?></div>
</div>