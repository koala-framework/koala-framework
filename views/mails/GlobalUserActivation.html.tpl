<?= trlKwf('Hello {0}!', htmlspecialchars($this->fullname)); ?><br /><br />

<?= trlKwf('Your account at {0}<br />has just been set active.', '<a href="'.htmlspecialchars($this->webUrl).'">'.htmlspecialchars($this->webUrl).'</a>'); ?><br />
<?= trlKwf('You may now log in to your account.'); ?><br />
<?= trlKwf('If you lost your password, you may use the Lost Password? service.'); ?><br /><br />

<?= htmlspecialchars($this->applicationName); ?><br /><br />

--<br />
<?= trlKwf('This email has been generated automatically. There may be no recipient if you answer to this email.'); ?>
