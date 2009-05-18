<?= trlVps('Hello {0}!', $this->fullname); ?><br /><br />

<?= trlVps('Your account at {0}
has successfully been created.', '<a href="'.$this->webUrl.'">'.$this->webUrl.'</a>'); ?><br />
<?= trlVps('Please use the following link to activate your account:'); ?><br />
<a href="<?= $this->activationUrl; ?>"><?= trlVps('Click here to proceed &raquo;'); ?></a>.<br /><br />

<?= trlVps('If the activation link does not work, please copy the following address and paste it in your browser (it's possible that the address has a line-break, so please be sure to copy everything correctly):'); ?><br /><br />

<?= $this->activationUrl; ?><br /><br />

<?= trlVps('The RSSinclude.com team');?><br /><br />

--<br />
<?= trlVps('This email has been generated automatically. There may be no recipient if you answer to this email.'); ?>
