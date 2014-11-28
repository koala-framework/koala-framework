<?= trlKwf('Hello {0}!', $this->fullname); ?>


<?= trlKwf('Your account at {0} has just been created.', $this->applicationName); ?>

1. <?= trlKwf('Please use the following link to choose yourself a password'); ?>:

<?= $this->activationUrl; ?>


2. <?= trlKwf('As soon as you have chosen a password you can login at the following Link'); ?>:
<?= $this->loginUrl; ?>


--
<?= trlKwf('This email has been generated automatically. There may be no recipient if you answer to this email.'); ?>
