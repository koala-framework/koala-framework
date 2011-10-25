<?= trlKwf('Hello {0}!', $this->name); ?><br /><br />

<?= trlKwf('A new entry has been written in your guestbook.'); ?><br />
<a href="<?= $this->url; ?>"><?= trlKwf('Click here to go directly to your profile'); ?></a><br /><br />

<?= trlKwf('This is the text that was saved in your guestbook:'); ?><br />
<?= $this->text; ?><br /><br />
