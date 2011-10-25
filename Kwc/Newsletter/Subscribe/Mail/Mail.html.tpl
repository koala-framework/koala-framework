<?= trlKwf('Hello {0}', trim($this->formRow->getMailFirstname().' '.$this->formRow->getMailLastname())); ?>!<br /><br />

<?= trlKwf('You have just been subscribed to the newsletter at {0}.', $this->host); ?><br /><br />

<? if ($this->doubleOptInComponentId) { ?>
    <?= '-- '.trlKwf('ACTIVATION LINK').' --'; ?><br />
    <a href="*showcomponent*<?= $this->doubleOptInComponentId; ?>*"><?= trlKwf('Please click here, to confirm your E-Mail address and to receive our newsletters in future.'); ?></a><br /><br />
<? } else if ($this->unsubscribeComponentId) { ?>
    <?= trlKwf('To unsubscribe anytime from our newsletter, click this link:'); ?><br />
    <a href="*showcomponent*<?= $this->unsubscribeComponentId; ?>*"><?= trlKwf('Unsubscribe'); ?></a><br /><br />
<? } ?>

<?= trlKwf('To change you data or settings, click this link:'); ?><br />
<a href="*showcomponent*<?= $this->editComponentId; ?>*"><?= trlKwf('Settings'); ?></a><br /><br />

<?= trlKwf('Thanks for your subscription!'); ?><br />
