<?= trlVps('A new post has been written in your Guestbook.'); ?>


<?= trlVps('Written by'); ?>: <?= $this->name; ?>

<?= trlVps('E-Mail'); ?>: <?= $this->email; ?>


<?= trlVps('Content:'); ?>

<?= $this->text; ?>


<? if ($this->activationType == Vpc_Guestbook_Component::INACTIVE_ON_SAVE) {
    echo trlVps('Click this link to activate the post on your website:');
    echo "\n*showcomponent*".$this->activateId."*&post_id=".$this->activatePostId;
} else if ($this->activationType == Vpc_Guestbook_Component::ACTIVE_ON_SAVE) {
    echo trlVps('If you wish to deactivate the post in your guestbook, click here:');
    echo "\n*showcomponent*".$this->activateId."*&post_id=".$this->activatePostId;
} ?>


<?= trlVps('Click this link to get to your guestbook:'); ?>

<?= $this->url; ?>

