<div class="<?=$this->rootElementClass?>">
    <h1><?=$this->data->trlKwf('Set a new password')?></h1>
    <p>
        <?=$this->data->trlKwf('Please enter in both fields the password which you want to use for your useraccount')?>.
    </p>
    <p>
        <?=$this->data->trlKwf('After entering your new password you are automatically logged in.')?>
    </p>

    <?=$this->component($this->form)?>
</div>