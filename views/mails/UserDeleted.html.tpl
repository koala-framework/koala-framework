<?= trlKwf('Hello {0}!', Kwf_Util_HtmlSpecialChars::filter($this->fullname)); ?><br /><br />

<?= trlKwf('Your account at {0} has been deleted.', '<a href="'.Kwf_Util_HtmlSpecialChars::filter($this->webUrl).'">'.Kwf_Util_HtmlSpecialChars::filter($this->webUrl).'</a>'); ?><br /><br />

<?= Kwf_Util_HtmlSpecialChars::filter($this->applicationName); ?><br /><br />

--<br />
<?= trlKwf('This email has been generated automatically. There may be no recipient if you answer to this email.'); ?>
