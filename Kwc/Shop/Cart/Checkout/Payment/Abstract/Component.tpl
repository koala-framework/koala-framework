<div class="<?=$this->rootElementClass?>">
    <h1><?=$this->data->trlKwf('Please check your details');?></h1>
    <?=$this->component($this->orderHeader)?>
    <?=$this->component($this->orderTable)?>
    <?=$this->component($this->confirmLink)?>
</div>