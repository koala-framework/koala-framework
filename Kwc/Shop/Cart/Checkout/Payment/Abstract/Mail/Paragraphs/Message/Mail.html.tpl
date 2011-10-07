<? if($this->order->comment) { ?>
    <p><?=trlKwf('Your message to {0}',Kwf_Registry::get("config")->application->name);?>:</p>
    <p><?=htmlspecialchars($this->order->comment);?></p>
<? } ?>