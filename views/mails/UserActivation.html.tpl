<?= trlKwf('Hello {0}!', $this->fullname); ?><br /><br />

<?= trlKwf('Your account at {0} has just been created.', $this->applicationName); ?><br />
1. <?= trlKwf('Please use the following link to choose yourself a password'); ?>:<br /><br />
<a href="<?= $this->activationUrl; ?>"><?= $this->activationUrl; ?></a><br /><br />
2. <?= trlKwf('As soon as you have chosen a password you can login at the following Link'); ?>:<br /><br />
<a href="<?= $this->loginUrl; ?>"><?= $this->loginUrl; ?></a><br /><br />

--<br />
<?= trlKwf('This email has been generated automatically. There may be no recipient if you answer to this email.'); ?>
