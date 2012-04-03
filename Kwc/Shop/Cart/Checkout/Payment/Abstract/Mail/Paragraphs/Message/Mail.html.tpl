<? if($this->order->comment) { ?>
    <p>
        <?=trlKwf('Your message to {0}',Kwf_Registry::get("config")->application->name);?>:<br />
        <?=htmlspecialchars($this->order->comment);?>
    </p>
<? } ?>