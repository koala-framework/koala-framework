<?= $this->data->trlKwf('Hello {0}!', $this->fullname); ?><br /><br />

<?= $this->data->trlKwf('Your account at {0} has been deleted.', '<a href="'.$this->webUrl.'">'.$this->webUrl.'</a>'); ?><br /><br />

<?= $this->applicationName; ?><br /><br />

--<br />
<?= $this->data->trlKwf('This email has been generated automatically. There may be no recipient if you answer to this email.'); ?>
