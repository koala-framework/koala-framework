<div class="<?=$this->cssClass?>">
    <h1><?=trlVps('Lost your password?')?></h1>
    <p>
        <?=trlVps('Please type in your {0} email address in the field below.', Vps_Registry::get('config')->application->name)?><br />
        <?=trlVps('You will receive an email with a passwort reset link and further instructions.')?>
    </p>

    <?=$this->component($this->form)?>
</div>