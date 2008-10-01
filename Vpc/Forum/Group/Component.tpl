<div class="<?=$this->cssClass?>">
    <span class="beforeButton"></span><span class="button"><div class="newThread first"><?=$this->componentLink($this->newThread, trlVps('Create a new topic'))?></div></span><span class="afterButton"></span>
    <?=$this->component($this->view)?>
    <div class="newThread"><?=$this->componentLink($this->newThread, trlVps('Create a new topic'))?></div>
</div>