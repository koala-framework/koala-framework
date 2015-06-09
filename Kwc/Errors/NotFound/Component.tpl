<div class="kwfup-webStandard" id="error404">
    <div class="left">
        <h1><?=$this->data->trlKwf('404 - File not found');?></h1>
        <p><?=$this->data->trlKwf('Sorry, that address');?> (<?=$this->requestUri;?>) <?=$this->data->trlKwf('does not exist at this site');?>.</p>
        <p>
            <ul>
                <li>
                    <?=$this->data->trlKwf('If you typed the address, make sure the spelling is correct.');?><br/>
                    <?=$this->data->trlKwf('Note: Most addresses are case sensitive');?>!!
                </li>
                <li><?=$this->data->trlKwf('Check the page you are coming from');?></li>
            </ul>
        </p>
        <p><strong><a href="/"><?=$this->data->trlKwf('Go back to main page');?></a></strong></p>
    </div>
    <div class="right">
        <img src="/assets/kwf/images/errorWarning.jpg" alt="" />
    </div>
    <div class="clear"></div>
</div>
