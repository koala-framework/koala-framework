<div class="<?=$this->cssClass?>">
    <h1><?=$this->data->trlKwf('Please login')?></h1>
    <p><?=$this->data->trlKwf('You have to login to see the requested page.')?></p>
    <? if ($this->register) { ?>
        <p><?=$this->data->trlKwf("If you don't have an account, you can")?>
        <?=$this->componentLink($this->register, $this->data->trlKwf('register here'))?>.
        </p>
    <? } ?>
    <? if ($this->lostPassword) { ?>
        <p><?=$this->data->trlKwf("If you have lost your password,")?>
        <?=$this->componentLink($this->lostPassword, $this->data->trlKwf('request a new one here'))?>.
        </p>
    <? } ?>
    <? if ($this->facebook) { ?>
        <?=$this->component($this->facebook)?>.
    <? } ?>
    <?=$this->component($this->form)?>
</div>
