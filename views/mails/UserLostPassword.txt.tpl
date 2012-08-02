<?= $this->data->trlKwf('Hello {0}!', $this->fullname); ?>


<?= $this->data->trlKwf('This email has been generated using the lost password function at {0}', $this->webUrl); ?>

<?= $this->data->trlKwf('Please use the following link to choose yourself a new password.'); ?>

<?= $this->lostPasswordUrl; ?>


<?= $this->data->trlKwf('If you did not request this email you may just ignore it and use the login as before.'); ?>


<?= $this->applicationName; ?>


--
<?= $this->data->trlKwf('This email has been generated automatically. There may be no recipient if you answer to this email.'); ?>
