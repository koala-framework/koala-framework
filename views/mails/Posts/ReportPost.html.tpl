<?= trlVps('Hello'); ?>!<br /><br />

<?= trlVps('A Post at {0} has been reported with the following comment', '<a href="'.$this->webUrl.'">'.$this->webUrl.'</a>'); ?>:<br /><br />

<?= $this->content; ?><br /><br />

<a href="<?= $this->webUrl.$this->postUrl; ?>"><?= trlVps('Click here'); ?></a>,
<?= trlVps('to go directly to the post or read it below'); ?>:<br /><br />

<?= nl2br($this->postContent); ?><br /><br /><br />


<?= $this->applicationName; ?><br /><br />

--<br />
<?= trlVps('This email has been generated automatically. There may be no recipient if you answer to this email.'); ?>
