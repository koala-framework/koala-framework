<?= trlKwf('Hello {0}!', Kwf_Util_HtmlSpecialChars::filter($this->fullname)); ?><br /><br />

<?= trlKwf('Your account at {0} has just been created.', Kwf_Util_HtmlSpecialChars::filter($this->applicationName)); ?><br />
1. <?= trlKwf('Please use the following link to choose yourself a password'); ?>:<br /><br />
<a href="<?= Kwf_Util_HtmlSpecialChars::filter($this->activationUrl); ?>"><?= Kwf_Util_HtmlSpecialChars::filter($this->activationUrl); ?></a><br /><br />
2. <?= trlKwf('As soon as you have chosen a password you can login at the following Link'); ?>:<br /><br />
<a href="<?= Kwf_Util_HtmlSpecialChars::filter($this->loginUrl); ?>"><?= Kwf_Util_HtmlSpecialChars::filter($this->loginUrl); ?></a><br /><br />

--<br />
<?= trlKwf('This email has been generated automatically. There may be no recipient if you answer to this email.'); ?>
