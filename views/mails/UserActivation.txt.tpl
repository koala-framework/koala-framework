<?= trlVps('Hello {0}!', $this->fullname); ?>


<?= trlVps('Your account at {0}
has just been created.', $this->webUrl); ?>

<?= trlVps('Please use the following link to choose yourself a password and to login'); ?>

<?= $this->activationUrl; ?>


<?= $this->applicationName; ?>


--
<?= trlVps('This email has been generated automatically. There may be no recipient if you answer to this email.'); ?>
