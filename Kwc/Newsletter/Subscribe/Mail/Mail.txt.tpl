<?= $this->data->trlKwf('Hello {0}', trim($this->formRow->getMailFirstname().' '.$this->formRow->getMailLastname())); ?>!

<?= $this->data->trlKwf('You have just been subscribed to the newsletter at {0}.', $this->host); ?>


<?php if ($this->doubleOptInComponentId) { ?>
<?= $this->data->trlKwf('Please click the following link to confirm your E-Mail address and to receive our newsletters in future.'); ?>

<?= '-- '.$this->data->trlKwf('ACTIVATION LINK').' --'; ?>

<?= $this->doubleOptInComponent->getAbsoluteUrl(); ?>

<?php } else if ($this->unsubscribeComponentId) { ?>
<?= $this->data->trlKwf('To unsubscribe anytime from our newsletter, click this link:'); ?>

<?= $this->unsubscribeComponent->getAbsoluteUrl(); ?>

<?php } ?>
<?= $this->data->trlKwf('To change you data or settings, click this link:'); ?>

<?= $this->editComponent->getAbsoluteUrl(); ?>


<?= $this->data->trlKwf('Thanks for your subscription!'); ?>

