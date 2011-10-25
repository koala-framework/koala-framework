<div class="webStandard" id="error404">
    <div class="left">
        <h1><?=trlKwf('404 - File not found');?></h1>
        <p><?=trlKwf('Sorry, that address');?> (<?=$this->requestUri;?>) <?=trlKwf('does not exist at this site');?>.</p>
        <p>
            <ul>
                <li>
                    <?=trlKwf('If you typed the address, make sure the spelling is correct.');?><br/>
                    <?=trlKwf('Note: Most addresses are also case sensitive');?>!!
                </li>
                <li><?=trlKwf('Check the page, you are comming from');?></li>
            </ul>
        </p>
        <p><strong><a href="/"><?=trlKwf('Go back to main page');?></a></strong></p>
    </div>
    <div class="right">
        <img src="/assets/kwf/images/errorWarning.jpg" alt="" />
    </div>
    <div class="clear"></div>
</div>
