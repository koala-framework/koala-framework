<?php if($this->userIsAuthed) { ?>
    <div class="<?=$this->cssClass?>">
    <?php if ($this->isObserved) { ?>
        <a class="observed" href="<?= $this->observeUrl ?>">Thema beobachten</a>
    <?php } else { ?>
        <a class="notObserved" href="<?= $this->observeUrl ?>">Thema beobachten</a>
    <?php } ?>
    </div>
<?php } ?>
