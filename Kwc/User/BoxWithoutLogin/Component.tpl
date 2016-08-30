<div class="<?=$this->rootElementClass?>">
    <ul>
        <?php if ($this->login) { ?>
            <li><?=$this->componentLink($this->login, $this->data->trlKwf('Login'))?><?=$this->linkPostfix?></li>
        <?php } ?>

        <?php if ($this->register) { ?>
            <li class="register"><?=$this->componentLink($this->register, $this->data->trlKwf('Register'))?><?=$this->linkPostfix?></li>
        <?php } ?>

        <?php if ($this->lostPassword) { ?>
            <li><?=$this->componentLink($this->lostPassword, $this->data->trlKwf('Lost password'))?><?=$this->linkPostfix?></li>
        <?php } ?>
    </ul>
</div>
