<div class="<?=$this->cssClass?>">
    <div class="back"><?=$this->componentLink($this->userProfile, trlVps('My Profile'))?></div>
    <h1 class="mainHeadline"><?=trlVps('Account - Properties')?></h1>
    <?=$this->component($this->form)?>
</div>
