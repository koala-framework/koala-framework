<div class="<?=$this->cssClass?>">
    <h2>
        <?=$this->componentLink($this->data->getParentPage())?> »
        <?=$this->componentLink($this->data)?>
    </h2>
    <?=$this->componentLink($this->newThread, trlVps('Create a new topic'))?>
    <?=$this->component($this->view)?>
    <?=$this->componentLink($this->newThread, trlVps('Create a new topic'))?>
</div>