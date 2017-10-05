<?php if ($this->order->comment) { ?>
    <p>
        <?=$this->data->trlKwf('Your message to {0}',Kwf_Registry::get("config")->application->name);?>:<br />
        <?=Kwf_Util_HtmlSpecialChars::filter($this->order->comment);?>
    </p>
<?php } ?>
