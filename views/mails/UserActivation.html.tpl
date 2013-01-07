<?= trlKwf('Hello {0}!', $this->fullname); ?><br /><br />

<?= trlKwf('Your account at {0} has just been created.', '<a href="'.$this->webUrl.'">'.$this->webUrl.'</a>'); ?><br />
<?= trlKwf('Please use the following link to choose yourself a password and to login'); ?><br />
<a href="<?= $this->activationUrl; ?>"><?= trlKwf('Click here to activate and log in to your Account'); ?></a>.<br /><br />

<?= trlKwf('If the activationlink does not work, copy the following address and paste it in your browser (it is possible that the address has a line-break, so please be sure to copy everything correctly):'); ?><br /><br />

<?= $this->activationUrl; ?><br /><br />

<?= $this->applicationName; ?><br /><br />

--<br />
<?= trlKwf('This email has been generated automatically. There may be no recipient if you answer to this email.'); ?>
