<?= trlVps('Hello {0}!', $this->name); ?><br /><br />

<?= trlVps('A new entry has been written in your guestbook.'); ?><br />
<a href="<?= $this->url; ?>"><?= trlVps('Click here to go directly to your profile'); ?></a><br /><br />

<?= trlVps('This is the text that was saved in your guestbook:'); ?><br />
<?= $this->text; ?><br /><br />
