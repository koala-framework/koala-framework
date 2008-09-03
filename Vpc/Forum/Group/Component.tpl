<div class="<?=$this->cssClass?>">
    <?=$this->component($this->breadCrumbs)?>
    <div class="newThread first"><?=$this->componentLink($this->newThread, trlVps('Create a new topic'))?></div>
    <?=$this->component($this->view)?>
    <div class="newThread"><?=$this->componentLink($this->newThread, trlVps('Create a new topic'))?></div>
</div>