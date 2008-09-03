<div class="<?=$this->cssClass?>">
    <h2>
        <?=$this->componentLink($this->data->getParentPage()->getParentPage()->getParentPage())?> »
        <?=$this->componentLink($this->data->getParentPage()->getParentPage())?> »
        <?=$this->componentLink($this->data->getParentPage())?>
    </h2>
    <?=$this->component($this->form)?>
    <?php if (!$this->isProcessed) { ?>
    <h2><?=trlVps('Last Posts')?>:</h2>
    <?=$this->component($this->lastPosts)?>
    <?php } ?>
</div>