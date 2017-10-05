<?=$this->data->trlKwf('Your message to {0}',Kwf_Registry::get("config")->application->name);?>: <?=Kwf_Util_HtmlSpecialChars::filter($this->order->comment);?>
