<?= $this->data->trlKwf('Hello {0}!', $this->fullname); ?><br /><br />

<?= $this->data->trlKwf('Your account at {0}<br />has just been set active.', '<a href="'.$this->webUrl.'">'.$this->webUrl.'</a>'); ?><br />
<?= $this->data->trlKwf('You may now log in to your account.'); ?><br />
<?= $this->data->trlKwf('If you lost your password, you may use the Lost Password? service.'); ?><br /><br />

<?= $this->applicationName; ?><br /><br />

--<br />
<?= $this->data->trlKwf('This email has been generated automatically. There may be no recipient if you answer to this email.'); ?>
