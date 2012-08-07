<?=$this->data->trlKwf('Your message to {0}',Kwf_Registry::get("config")->application->name);?>: <?=htmlspecialchars($this->order->comment);?>
