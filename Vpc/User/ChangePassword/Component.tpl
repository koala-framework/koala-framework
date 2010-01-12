<div class="<?=$this->cssClass?>">
    <div class="back"><?=$this->componentLink($this->userProfile, trlVps('Show my Profile'))?></div>
    <h1 class="mainHeadline"><?=trlVps('Account - Change Password')?></h1>
    <?=$this->component($this->form)?>
</div>
