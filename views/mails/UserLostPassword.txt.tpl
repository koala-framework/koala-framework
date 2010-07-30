<?= trlVps('Hello {0}!', $this->fullname); ?>


<?= trlVps('This email has been generated using the lost password function at {0}', $this->webUrl); ?>

<?= trlVps('Please use the following link to choose yourself a new password.'); ?>

<?= $this->lostPasswordUrl; ?>


<?= trlVps('If you did not request this email you may just ignore it and use the login as before.'); ?>


<?= $this->applicationName; ?>


--
<?= trlVps('This email has been generated automatically. There may be no recipient if you answer to this email.'); ?>
