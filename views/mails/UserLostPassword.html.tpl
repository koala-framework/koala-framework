<?= trlKwf('Hello {0}!', Kwf_Util_HtmlSpecialChars::filter($this->fullname)); ?><br /><br />

<?= trlKwf('This email has been generated using the lost password function at {0}', '<a href="'.Kwf_Util_HtmlSpecialChars::filter($this->webUrl).'">'.Kwf_Util_HtmlSpecialChars::filter($this->webUrl).'</a>'); ?><br />
<?= trlKwf('Please use the following link to choose yourself a new password.'); ?><br />
<a href="<?= Kwf_Util_HtmlSpecialChars::filter($this->lostPasswordUrl); ?>"><?= trlKwf('Click here to choose a new password'); ?></a>.<br /><br />

<?= trlKwf('If the activationlink does not work, copy the following address and paste it in your browser (it is possible that the address has a line-break, so please be sure to copy everything correctly):'); ?><br /><br />

<?= Kwf_Util_HtmlSpecialChars::filter($this->lostPasswordUrl); ?><br /><br />

<?= trlKwf('If you did not request this email you may just ignore it and use the login as before.'); ?><br /><br />

<?= Kwf_Util_HtmlSpecialChars::filter($this->applicationName); ?><br /><br />

--<br />
<?= trlKwf('This email has been generated automatically. There may be no recipient if you answer to this email.'); ?>
