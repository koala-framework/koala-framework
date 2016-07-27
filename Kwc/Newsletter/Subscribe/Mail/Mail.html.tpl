<?= $this->data->trlKwf('Hello {0}', trim($this->formRow->getMailFirstname().' '.$this->formRow->getMailLastname())); ?>!<br /><br />

<?= $this->data->trlKwf('You have just been subscribed to the newsletter at {0}.', $this->host); ?><br /><br />

<?php if ($this->doubleOptInComponentId) { ?>
    <?= '-- '.$this->data->trlKwf('ACTIVATION LINK').' --'; ?><br />
    <a href="*showcomponent*<?= $this->doubleOptInComponentId; ?>*"><?= $this->data->trlKwf('Please click here, to confirm your E-Mail address and to receive our newsletters in future.'); ?></a><br /><br />
<?php } else if ($this->unsubscribeComponentId) { ?>
    <?= $this->data->trlKwf('To unsubscribe anytime from our newsletter, click this link:'); ?><br />
    <a href="*showcomponent*<?= $this->unsubscribeComponentId; ?>*"><?= $this->data->trlKwf('Unsubscribe'); ?></a><br /><br />
<?php } ?>

<?= $this->data->trlKwf('To change you data or settings, click this link:'); ?><br />
<a href="*showcomponent*<?= $this->editComponentId; ?>*"><?= $this->data->trlKwf('Settings'); ?></a><br /><br />

<?= $this->data->trlKwf('Thanks for your subscription!'); ?><br />
