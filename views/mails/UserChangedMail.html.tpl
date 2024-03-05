<?= trlKwf('Hello {0}!', Kwf_Util_HtmlSpecialChars::filter($this->fullname)); ?><br /><br />

<?= trlKwf('Your email address at {0} has been changed.', '<a href="'.Kwf_Util_HtmlSpecialChars::filter($this->webUrl).'">'.Kwf_Util_HtmlSpecialChars::filter($this->webUrl).'</a>'); ?><br />
<?= trlKwf('Your old email address was {0}, the new one is {1}', array(Kwf_Util_HtmlSpecialChars::filter($this->oldMail), Kwf_Util_HtmlSpecialChars::filter($this->userData['email']))); ?><br /><br />

<?= Kwf_Util_HtmlSpecialChars::filter($this->applicationName); ?><br /><br />

--<br />
<?= trlKwf('This email has been generated automatically. There may be no recipient if you answer to this email.'); ?>
