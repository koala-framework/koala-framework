<?= trlVps('Hello {0}', trim($this->formRow->getMailFirstname().' '.$this->formRow->getMailLastname())); ?>!

<?= trlVps('You have just been subscribed to the newsletter at {0}.', $this->host); ?>


<? if ($this->doubleOptInComponentId) { ?>
<?= trlVps('Please click the following link to confirm your E-Mail address and to receive our newsletters in future.'); ?>

<?= '-- '.trlVps('ACTIVATION LINK').' --'; ?>

*showcomponent*<?= $this->doubleOptInComponentId; ?>*

<? } else if ($this->unsubscribeComponentId) { ?>
<?= trlVps('To unsubscribe anytime from our newsletter, click this link:'); ?>

*showcomponent*<?= $this->unsubscribeComponentId; ?>*

<? } ?>
<?= trlVps('To change you data or settings, click this link:'); ?>

*showcomponent*<?= $this->editComponentId; ?>*


<?= trlVps('Thanks for your subscription!'); ?>

