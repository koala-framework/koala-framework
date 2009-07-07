<div class="<?=$this->cssClass?>">
    <h1><?=trlVps('Please check your petitions');?></h1>
    <?=$this->component($this->orderHeader)?>
    <?=$this->component($this->orderTable)?>
    <?=$this->component($this->confirmLink)?>
</div>