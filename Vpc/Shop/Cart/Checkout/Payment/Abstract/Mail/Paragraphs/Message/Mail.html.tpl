<? if($this->order->comment) { ?>
    <p><?=trlVps('Your message to {0}',Vps_Registry::get("config")->application->name);?>:</p>
    <?= $this->order->comment; ?>
<? } ?>