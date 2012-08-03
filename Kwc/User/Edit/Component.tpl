<div class="<?=$this->cssClass?>">
    <div class="back"><?=$this->componentLink($this->userProfile, $this->data->trlKwf('Show my Profile'))?></div>
    <h1 class="mainHeadline"><?=$this->data->trlKwf('Account - Properties')?></h1>
    <?=$this->component($this->form)?>
</div>
