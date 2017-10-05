<?= $this->data->trlKwf('Hello {0}!', Kwf_Util_HtmlSpecialChars::filter($this->name)); ?>


<?= $this->data->trlKwf('A new entry has been written in your guestbook.'); ?>

<?= $this->data->trlKwf('Here is the Link to your profile:'); ?>

<?= $this->url; ?>


<?= $this->data->trlKwf('This is the text that was saved in your guestbook:'); ?>

<?= Kwf_Util_HtmlSpecialChars::filter($this->text); ?>
