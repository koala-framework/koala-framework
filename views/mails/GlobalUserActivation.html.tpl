<?= trlKwf('Hello {0}!', Kwf_Util_HtmlSpecialChars::filter($this->fullname)); ?><br /><br />

<?= trlKwf('Your account at {0}<br />has just been set active.', '<a href="'.Kwf_Util_HtmlSpecialChars::filter($this->webUrl).'">'.Kwf_Util_HtmlSpecialChars::filter($this->webUrl).'</a>'); ?><br />
<?= trlKwf('You may now log in to your account.'); ?><br />
<?= trlKwf('If you lost your password, you may use the Lost Password? service.'); ?><br /><br />

<?= Kwf_Util_HtmlSpecialChars::filter($this->applicationName); ?><br /><br />

--<br />
<?= trlKwf('This email has been generated automatically. There may be no recipient if you answer to this email.'); ?>
