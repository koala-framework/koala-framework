<div class="<?=$this->cssClass?>">
    <ul>
        <li><?=$this->componentLink($this->login, trlKwf('Login'))?><?=$this->linkPostfix?></li>
        <li class="register"><?=$this->componentLink($this->register, trlKwf('Register'))?><?=$this->linkPostfix?></li>
        <? if ($this->lostPassword) { ?>
        <li><?=$this->componentLink($this->lostPassword, trlKwf('Lost password'))?><?=$this->linkPostfix?></li>
        <? } ?>
    </ul>
</div>
