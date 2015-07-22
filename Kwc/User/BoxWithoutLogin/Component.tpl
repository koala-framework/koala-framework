<div class="<?=$this->rootElementClass?>">
    <ul>
        <? if ($this->login) { ?>
            <li><?=$this->componentLink($this->login, $this->data->trlKwf('Login'))?><?=$this->linkPostfix?></li>
        <? } ?>

        <? if ($this->register) { ?>
            <li class="register"><?=$this->componentLink($this->register, $this->data->trlKwf('Register'))?><?=$this->linkPostfix?></li>
        <? } ?>

        <? if ($this->lostPassword) { ?>
            <li><?=$this->componentLink($this->lostPassword, $this->data->trlKwf('Lost password'))?><?=$this->linkPostfix?></li>
        <? } ?>
    </ul>
</div>
