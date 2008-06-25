<?= trlVps('Hello {0}!', $this->fullname); ?><br /><br />

<?= trlVps('A new entry has been written in your guestbook.'); ?><br />
<a href="<?= $this->webUrl.$this->profileUrl; ?>"><?= trlVps('Click here to go directly to you profile'); ?></a><br /><br />

<?= trlVps('This is the text that was saved in your guestbook:'); ?><br />
<?= $this->content; ?><br /><br />

<?= $this->applicationName; ?><br /><br />

--<br />
<?= trlVps('This email has been generated automatically. There may be no recipient if you answer to this email.'); ?>
