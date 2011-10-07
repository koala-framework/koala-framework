<?= trlVps('Hello {0}', trim($this->formRow->getMailFirstname().' '.$this->formRow->getMailLastname())); ?>!<br /><br />

<?= trlVps('You have just been subscribed to the newsletter at {0}.', $this->host); ?><br /><br />

<? if ($this->doubleOptInComponentId) { ?>
    <?= '-- '.trlVps('ACTIVATION LINK').' --'; ?><br />
    <a href="*showcomponent*<?= $this->doubleOptInComponentId; ?>*"><?= trlVps('Please click here, to confirm your E-Mail address and to receive our newsletters in future.'); ?></a><br /><br />
<? } else if ($this->unsubscribeComponentId) { ?>
    <?= trlVps('To unsubscribe anytime from our newsletter, click this link:'); ?><br />
    <a href="*showcomponent*<?= $this->unsubscribeComponentId; ?>*"><?= trlVps('Unsubscribe'); ?></a><br /><br />
<? } ?>

<?= trlVps('To change you data or settings, click this link:'); ?><br />
<a href="*showcomponent*<?= $this->editComponentId; ?>*"><?= trlVps('Settings'); ?></a><br /><br />

<?= trlVps('Thanks for your subscription!'); ?><br />
