<div class="<?=$this->cssClass?>">
    <h1 class="mainHeadline"><?=trlKwf('Lost your password?')?></h1>
    <p>
        <?=trlKwf('Please type in your {0} email address in the field below.', Kwf_Registry::get('config')->application->name)?><br />
        <?=trlKwf('You will receive an email with a passwort reset link and further instructions.')?>
    </p>

    <?=$this->component($this->form)?>
</div>