<?= $this->data->trlKwf('A new post has been written in your Guestbook.'); ?>


<?= $this->data->trlKwf('Written by'); ?>: <?= $this->name; ?>

<?= $this->data->trlKwf('E-Mail'); ?>: <?= $this->email; ?>


<?= $this->data->trlKwf('Content:'); ?>

<?= $this->text; ?>


<?php if ($this->activationType == Kwc_Guestbook_Component::INACTIVE_ON_SAVE) {
    echo $this->data->trlKwf('Click this link to activate the post on your website:');
    echo "\n".$this->activateComponent->getAbsoluteUrl()."?post_id=".$this->activatePostId;
} else if ($this->activationType == Kwc_Guestbook_Component::ACTIVE_ON_SAVE) {
    echo $this->data->trlKwf('If you wish to deactivate the post in your guestbook, click here:');
    echo "\n".$this->activateComponent->getAbsoluteUrl()."?post_id=".$this->activatePostId;
} ?>


<?= $this->data->trlKwf('Click this link to get to your guestbook:'); ?>

<?= $this->url; ?>

