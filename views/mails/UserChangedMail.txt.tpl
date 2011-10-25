<?= trlKwf('Hello {0}!', $this->fullname); ?>


<?= trlKwf('Your email address at {0} has been changed.', $this->webUrl); ?>

<?= trlKwf('Your old email address was {0}, the new one is {1}', array($this->oldMail, $this->userData['email'])); ?>


<?= $this->applicationName; ?>


--
<?= trlKwf('This email has been generated automatically. There may be no recipient if you answer to this email.'); ?>
