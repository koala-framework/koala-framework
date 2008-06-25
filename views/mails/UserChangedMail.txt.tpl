<?= trlVps('Hello {0}!', $this->fullname); ?>


<?= trlVps('Your email address at {0} has been changed.', $this->webUrl); ?>

<?= trlVps('Your old email address was {0}, the new one is {1}', array($this->oldMail, $this->userData['email'])); ?>


<?= $this->applicationName; ?>


--
<?= trlVps('This email has been generated automatically. There may be no recipient if you answer to this email.'); ?>
