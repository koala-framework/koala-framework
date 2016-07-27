<div class="<?=$this->rootElementClass?>">
    <?php if ($this->userProfile) { ?>
        <div class="back"><?=$this->componentLink($this->userProfile, $this->data->trlKwf('Show my Profile'))?></div>
    <?php } ?>
    <h1 class="mainHeadline"><?=$this->data->trlKwf('Account - Properties')?></h1>
    <?=$this->component($this->form)?>
</div>
