<?=Kwf_Util_HtmlSpecialChars::filter(trim($this->order->title.' '.$this->order->firstname));?> <?=Kwf_Util_HtmlSpecialChars::filter($this->order->lastname);?>

<?=Kwf_Util_HtmlSpecialChars::filter($this->order->street);?>

<?=Kwf_Util_HtmlSpecialChars::filter($this->order->zip);?> <?=Kwf_Util_HtmlSpecialChars::filter($this->order->city);?>

<?=Kwf_Util_HtmlSpecialChars::filter($this->order->country);?>
