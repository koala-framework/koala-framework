<?= $this->data->trlKwf('Hello {0}!', htmlspecialchars($this->name)); ?><br /><br />

<?= $this->data->trlKwf('A new entry has been written in your guestbook.'); ?><br />
<a href="<?= $this->url; ?>"><?= $this->data->trlKwf('Click here to go directly to your profile'); ?></a><br /><br />

<?= $this->data->trlKwf('This is the text that was saved in your guestbook:'); ?><br />
<?= htmlspecialchars($this->text); ?><br /><br />
