<div class="<?=$this->cssClass?>">
    <div class="path">
        <h2>
            <?=$this->componentLink($this->data->getParentPage()->getParentPage())?> »
            <?=$this->componentLink($this->data->getParentPage())?> »
            <?=$this->componentLink($this->data)?>
        </h2>
    </div>
    <?=$this->component($this->observe)?>
    <?php if (!$this->threadClosed) { ?>
        <div class="newPost"><?=$this->componentLink($this->write, trlVps('add Comment'))?></div>
    <?php } else { ?>
        <span class="threadClosed"><?= trlVps('Thread closed') ?></span>
    <?php } ?>
    <?=$this->component($this->paging)?>
    <? foreach($this->items as $post) { ?>
        <?=$this->component($post)?>
    <? } ?>
    <?=$this->component($this->paging)?>
    <?=$this->component($this->observe)?>
    <?php if (!$this->threadClosed) { ?>
        <div class="newPost"><?=$this->componentLink($this->write, trlVps('add Comment'))?></div>
    <?php } else { ?>
        <span class="threadClosed"><?= trlVps('Thread closed') ?></span>
    <?php } ?>
    <? if ($this->mayModerate) { ?>
    <?=$this->component($this->moderate)?>
    <?php } ?>
</div>