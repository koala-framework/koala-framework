<div class="webStandard" id="error404">
    <div class="left">
        <h1><?=trlVps('404 - File not found');?></h1>
        <p><?=trlVps('Sorry, that address');?> (<?=$this->requestUri;?>) <?=trlVps('does not exist at this site');?>.</p>
        <p>
            <ul>
                <li>
                    <?=trlVps('If you typed the address, make sure the spelling is correct.');?><br/>
                    <?=trlVps('Note: Most addresses are also case sensitive');?>!!
                </li>
                <li><?=trlVps('Check the page, you are comming from');?></li>
            </ul>
        </p>
        <p><strong><a href="/"><?=trlVps('Go back to main page');?></a></strong></p>
    </div>
    <div class="right">
        <img src="/assets/vps/images/errorWarning.jpg" alt="" />
    </div>
    <div class="clear"></div>
</div>
