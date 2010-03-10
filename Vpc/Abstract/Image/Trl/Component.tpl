<? if ($this->ownImage) { ?>
<?=$this->component($this->ownImage) ?>
<? } else { ?>
<? include($this->linkTemplate) ?>
<? } ?>