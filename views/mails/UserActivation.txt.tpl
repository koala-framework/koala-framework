<?= trlVps('Hello {0}!', $this->fullname); ?>


<?= trlVps('Your account at {0}
has successfully been created.', $this->webUrl); ?>

<?= trlVps('Please use the following link to activate your account and choose yourself a password:'); ?>

<?= $this->activationUrl; ?>


Your RSSinclude.com team

--
<?= trlVps('This email has been generated automatically. There may be no recipient if you answer to this email.'); ?>
