<div class="<?=$this->cssClass?>">
    <ul>
        <li><?=$this->componentLink($this->login, trlVps('Login'))?><?=$this->linkPostfix?></li>
        <li class="register"><?=$this->componentLink($this->register, trlVps('Register'))?><?=$this->linkPostfix?></li>
        <? if ($this->lostPassword) { ?>
        <li><?=$this->componentLink($this->lostPassword, trlVps('Lost password'))?><?=$this->linkPostfix?></li>
        <? } ?>
    </ul>
</div>
