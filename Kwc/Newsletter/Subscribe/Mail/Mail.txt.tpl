<?= $this->data->trlKwf('Hello {0}', trim($this->formRow->getMailFirstname().' '.$this->formRow->getMailLastname())); ?>!

<?= $this->data->trlKwf('You have just been subscribed to the newsletter at {0}.', $this->host); ?>


<?= $this->data->trlKwf('Please click the following link to confirm your E-Mail address and to receive our newsletters in future.'); ?>

<?= '-- '.$this->data->trlKwf('ACTIVATION LINK').' --'; ?>

<?= $this->doubleOptInComponent->getAbsoluteUrl(); ?>

<?= $this->data->trlKwf('To change you data or settings, click this link:'); ?>

<?= $this->editComponent->getAbsoluteUrl(); ?>


<?= $this->data->trlKwf('Thanks for your subscription!'); ?>

