<?= $this->data->trlKwf('Hello {0}!', $this->fullname); ?><br /><br />

<?= $this->data->trlKwf('Your account at {0}
has just been created.', '<a href="'.$this->webUrl.'">'.$this->webUrl.'</a>'); ?><br />
<?= $this->data->trlKwf('Please use the following link to choose yourself a password and to login'); ?><br />
<a href="<?= $this->activationUrl; ?>"><?= $this->data->trlKwf('Click here to activate and log in to your Account'); ?></a>.<br /><br />

<?= $this->data->trlKwf('If the activationlink does not work, copy the following address and paste it in your browser (it is possible that the address has a line-break, so please be sure to copy everything correctly):'); ?><br /><br />

<?= $this->activationUrl; ?><br /><br />

<?= $this->applicationName; ?><br /><br />

--<br />
<?= $this->data->trlKwf('This email has been generated automatically. There may be no recipient if you answer to this email.'); ?>
