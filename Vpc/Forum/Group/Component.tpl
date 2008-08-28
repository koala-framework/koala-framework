<div class="<?=$this->cssClass?>">
    <div class="path">
        <h2>
            <?=$this->componentLink($this->data->getParentPage())?> »
            <?=$this->componentLink($this->data)?>
        </h2>
    </div>
    <div class="newThread first"><?=$this->componentLink($this->newThread, trlVps('Create a new topic'))?></div>
    <?=$this->component($this->view)?>
    <div class="newThread"><?=$this->componentLink($this->newThread, trlVps('Create a new topic'))?></div>
</div>