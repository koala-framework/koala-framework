<?= trlKwf('Hello {0}!', htmlspecialchars($this->fullname)); ?><br /><br />

<?= trlKwf('Your account at {0} has been deleted.', '<a href="'.$this->webUrl.'">'.$this->webUrl.'</a>'); ?><br /><br />

<?= $this->applicationName; ?><br /><br />

--<br />
<?= trlKwf('This email has been generated automatically. There may be no recipient if you answer to this email.'); ?>
