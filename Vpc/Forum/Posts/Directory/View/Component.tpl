<div class="<?=$this->cssClass?>">
    <h2>
        <?=$this->componentLink($this->data->getParentPage()->getParentPage())?> »
        <?=$this->componentLink($this->data->getParentPage())?> »
        <?=$this->componentLink($this->data)?>
    </h2>
    
    <?=$this->component($this->observe)?>

    <?php if (!$this->threadClosed) { ?>
        <?=$this->componentLink($this->write, trlVps('add Comment'))?>
    <?php } else { ?>
        <span class="threadClosed">Thread closed</span>
    <?php } ?>
    <?=$this->component($this->paging)?>
    <? foreach($this->items as $post) { ?>
        <?=$this->component($post)?>
    <? } ?>
    <?=$this->component($this->paging)?>
    <?php if (!$this->threadClosed) { ?>
        <?=$this->componentLink($this->write, trlVps('add Comment'))?>
    <?php } else { ?>
        <span class="threadClosed">Thread closed</span>
    <?php } ?>
</div>