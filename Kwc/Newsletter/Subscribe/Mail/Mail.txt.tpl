<?= trlKwf('Hello {0}', trim($this->formRow->getMailFirstname().' '.$this->formRow->getMailLastname())); ?>!

<?= trlKwf('You have just been subscribed to the newsletter at {0}.', $this->host); ?>


<? if ($this->doubleOptInComponentId) { ?>
<?= trlKwf('Please click the following link to confirm your E-Mail address and to receive our newsletters in future.'); ?>

<?= '-- '.trlKwf('ACTIVATION LINK').' --'; ?>

*showcomponent*<?= $this->doubleOptInComponentId; ?>*

<? } else if ($this->unsubscribeComponentId) { ?>
<?= trlKwf('To unsubscribe anytime from our newsletter, click this link:'); ?>

*showcomponent*<?= $this->unsubscribeComponentId; ?>*

<? } ?>
<?= trlKwf('To change you data or settings, click this link:'); ?>

*showcomponent*<?= $this->editComponentId; ?>*


<?= trlKwf('Thanks for your subscription!'); ?>

