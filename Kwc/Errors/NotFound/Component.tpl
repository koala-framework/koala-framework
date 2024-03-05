<div class="<?=$this->rootElementClass?>">
    <h1>404 - <?=$this->data->trlKwf('File not found');?></h1>
    <p><?=$this->data->trlKwf('The requested URL "{0}" was not found on this server.', Kwf_Util_HtmlSpecialChars::filter($this->requestUri));?></p>
    <ul>
        <li>
            <?=$this->data->trlKwf('If you typed the address, make sure the spelling is correct');?>.<br/>
        </li>
        <li>
            <?=$this->data->trlKwf('Check the page you are coming from');?>.<br/><br/>
        </li>
    </ul>
    <p><strong><a href="/"><?=$this->data->trlKwf('Go back to main page');?></a></strong></p>
</div>
