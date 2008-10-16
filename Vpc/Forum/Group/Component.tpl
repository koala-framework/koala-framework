<div class="<?=$this->cssClass?>">
    <span class="beforeButton"></span><span class="button"><div class="newThread first"><?=$this->componentLink($this->newThread, trlVps('Create a new topic'))?></div></span><span class="afterButton"></span>
    <div class="clear"></div>
    <?=$this->component($this->view)?>
    <span class="beforeButton"></span><span class="button"><div class="newThread"><?=$this->componentLink($this->newThread, trlVps('Create a new topic'))?></div></span><span class="afterButton"></span>
</div>