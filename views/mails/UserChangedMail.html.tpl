<?= trlKwf('Hello {0}!', htmlspecialchars($this->fullname)); ?><br /><br />

<?= trlKwf('Your email address at {0} has been changed.', '<a href="'.$this->webUrl.'">'.$this->webUrl.'</a>'); ?><br />
<?= trlKwf('Your old email address was {0}, the new one is {1}', array($this->oldMail, $this->userData['email'])); ?><br /><br />

<?= $this->applicationName; ?><br /><br />

--<br />
<?= trlKwf('This email has been generated automatically. There may be no recipient if you answer to this email.'); ?>
