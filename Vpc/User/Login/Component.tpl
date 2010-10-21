<div class="<?=$this->cssClass?>">
    <h1><?=$this->data->trlVps('Please login')?></h1>
    <p><?=$this->data->trlVps('You have to login to see the requested page.')?></p>
    <? if ($this->register) { ?>
        <p><?=$this->data->trlVps("If you don't have an account, you can")?>
        <?=$this->componentLink($this->register, $this->data->trlVps('register here'))?>.
        </p>
    <? } ?>
    <? if ($this->lostPassword) { ?>
        <p><?=$this->data->trlVps("If you have lost your password,")?>
        <?=$this->componentLink($this->lostPassword, $this->data->trlVps('request a new one here'))?>.
        </p>
    <? } ?>
    <?=$this->component($this->form)?>
</div>