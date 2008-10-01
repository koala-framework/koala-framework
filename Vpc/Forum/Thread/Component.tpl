<div class="<?=$this->cssClass?>">

    <?=$this->component($this->observe)?>

    <?php if (!$this->threadClosed) { ?>
        <span class="beforeButton"></span><span class="button"><div class="newPost"><?=$this->componentLink($this->write)?></div></span><span class="afterButton"></span>
    <?php } else { ?>
        <span class="threadClosed"><?= trlVps('Thread closed') ?></span>
    <?php } ?>

    <?=$this->component($this->posts)?>

    <?=$this->component($this->observe)?>

    <?php if (!$this->threadClosed) { ?>
        <span class="beforeButton"></span><span class="button"><div class="newPost"><?=$this->componentLink($this->write)?></div></span><span class="afterButton"></span>
    <?php } else { ?>
        <span class="threadClosed"><?= trlVps('Thread closed') ?></span>
    <?php } ?>

    <?=$this->component($this->moderate)?>
</div>