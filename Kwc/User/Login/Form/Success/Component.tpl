<div class="<?=$this->cssClass?>">
    <input type="hidden" class="redirectTo" value="<?=$this->redirectTo->url?>" />
    <h1><?=trlKwf('Logged In')?></h1>
    <p><?=trlKwf('You have been logged in sucessfully.')?></p>
    <p><?=trlKwf("If the needed page doesn't load automatically,")?>
    <?=$this->componentLink($this->redirectTo, trlKwf('please click here'))?>.</p>
</div>