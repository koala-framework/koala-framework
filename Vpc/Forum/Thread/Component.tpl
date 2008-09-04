<div class="<?=$this->cssClass?>">

    <?=$this->component($this->observe)?>

    <?php if (!$this->threadClosed) { ?>
        <div class="newPost"><?=$this->componentLink($this->write)?></div>
    <?php } else { ?>
        <span class="threadClosed"><?= trlVps('Thread closed') ?></span>
    <?php } ?>

    <?=$this->component($this->posts)?>

    <?=$this->component($this->observe)?>

    <?php if (!$this->threadClosed) { ?>
        <div class="newPost"><?=$this->componentLink($this->write)?></div>
    <?php } else { ?>
        <span class="threadClosed"><?= trlVps('Thread closed') ?></span>
    <?php } ?>
    <? if ($this->mayModerate) { ?>
    <?=$this->component($this->moderate)?>
    <?php } ?>
</div>