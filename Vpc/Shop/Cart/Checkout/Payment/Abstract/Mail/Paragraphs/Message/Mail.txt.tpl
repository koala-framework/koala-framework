<?=trlVps('Your message to {0}',Vps_Registry::get("config")->application->name);?>: <?=htmlspecialchars($this->order->comment);?>
