<?= $this->data->trlKwf('Hello {0}', trim($this->formRow->getMailFirstname().' '.$this->formRow->getMailLastname())); ?>!<br /><br />

<?= $this->data->trlKwf('You have just been subscribed to the newsletter at {0}.', $this->host); ?><br /><br />

<?= '-- '.$this->data->trlKwf('ACTIVATION LINK').' --'; ?><br />
<?=$this->componentLink($this->doubleOptInComponent, $this->data->trlKwf('Please click here, to confirm your E-Mail address and to receive our newsletters in future.')); ?><br /><br />

<?= $this->data->trlKwf('To change you data or settings, click this link:'); ?><br />
<?=$this->componentLink($this->editComponent, $this->data->trlKwf('Settings')); ?><br /><br />

<?= $this->data->trlKwf('Thanks for your subscription!'); ?><br />
