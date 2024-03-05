<div class="kwfUp-content">
    <h1>
        <?php if ($this->isActivated) { ?>
            <?=trlKwf('Reset Password')?>
        <?php } else { ?>
            <?=trlKwf('Activate Useraccount')?>
        <?php } ?>
    </h1>
    <p>
        <?=trlKwf('Please type in your password. After clicking the button below')?>
        <?=trlKwf('you are logged in automatically and may use the typed in password')?>
        <?=trlKwf('for future logins.')?>
    </p>
    <p>
        <?=trlKwf('Your email address:')?> <strong><?=Kwf_Util_HtmlSpecialChars::filter($this->email)?></strong>
    </p>

    <?=$this->errorsHtml?>
    <form action="<?= Kwf_Util_HtmlSpecialChars::filter($this->action) ?>" method="<?=$this->method?>">
        <?php $this->formField($this->form) ?>
        <button class="submit" type="submit" name="<?= $this->formName ?>" value="submit"><?=trlKwf('Activate and login account')?></button>
    </form>

</div>
