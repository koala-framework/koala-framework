<?= trlVps('Hello {0}!', $this->fullname); ?><br /><br />

<?= trlVps('Your account at {0}<br />has just been set active.', '<a href="'.$this->webUrl.'">'.$this->webUrl.'</a>'); ?><br />
<?= trlVps('You may now log in to your account.'); ?><br />
<?= trlVps('If you lost your password, you may use the Lost Password? service.'); ?><br /><br />

<?= $this->applicationName; ?><br /><br />

--<br />
<?= trlVps('This email has been generated automatically. There may be no recipient if you answer to this email.'); ?>
