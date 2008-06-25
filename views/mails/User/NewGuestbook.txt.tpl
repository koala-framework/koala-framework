<?= trlVps('Hello {0}!', $this->fullname); ?>

<?= trlVps('A new entry has been written in your guestbook.'); ?>
<?= trlVps('Here is the Link to your profile:'); ?>
<?= $this->webUrl.$this->profileUrl; ?>

<?= trlVps('This is the text that was saved in your guestbook:'); ?>
<?= $this->content; ?>

<?= $this->applicationName; ?>

--
<?= trlVps('This email has been generated automatically. There may be no recipient if you answer to this email.'); ?>
