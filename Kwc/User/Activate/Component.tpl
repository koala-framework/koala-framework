<div class="<?=$this->cssClass?>">
    <h1><?=$this->data->trlKwf('Activate Useraccount')?></h1>
    <p>
        <?=$this->data->trlKwf('Plese enter in both fields the password which you want to use for your useraccount')?>.<br />
        <?=$this->data->trlKwf('After the activation you are automatically logged in and you could use your account.')?>
    </p>

    <?=$this->component($this->form)?>
</div>