<?php if($this->userIsAuthed) { ?>
    <div class="<?=$this->cssClass?>">
    <?php if ($this->isObserved) { ?>
        <a class="observed" href="<?= $this->observeUrl ?>"><?=trlVps('Observe Thread')?></a>
    <?php } else { ?>
        <a class="notObserved" href="<?= $this->observeUrl ?>"><?=trlVps('Observe Thread')?></a>
    <?php } ?>
    </div>
<?php } ?>
