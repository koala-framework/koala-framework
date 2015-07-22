<div class="<?=$this->rootElementClass?>">
    <h1 class="mainHeadline"><?=$this->data->trlKwf('Lost your password?')?></h1>
    <p>
        <?=$this->data->trlKwf('Please type in your {0} email address in the field below.', Kwf_Registry::get('config')->application->name)?>
    </p>
    <p>
        <?=$this->data->trlKwf('You will receive an email with a password reset link and further instructions.')?>
    </p>

    <?=$this->component($this->form)?>
</div>
