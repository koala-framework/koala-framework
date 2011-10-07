<?= trlKwf('A new post has been written in your Guestbook.'); ?>


<?= trlKwf('Written by'); ?>: <?= $this->name; ?>

<?= trlKwf('E-Mail'); ?>: <?= $this->email; ?>


<?= trlKwf('Content:'); ?>

<?= $this->text; ?>


<? if ($this->activationType == Kwc_Guestbook_Component::INACTIVE_ON_SAVE) {
    echo trlKwf('Click this link to activate the post on your website:');
    echo "\n*showcomponent*".$this->activateId."*&post_id=".$this->activatePostId;
} else if ($this->activationType == Kwc_Guestbook_Component::ACTIVE_ON_SAVE) {
    echo trlKwf('If you wish to deactivate the post in your guestbook, click here:');
    echo "\n*showcomponent*".$this->activateId."*&post_id=".$this->activatePostId;
} ?>


<?= trlKwf('Click this link to get to your guestbook:'); ?>

<?= $this->url; ?>

