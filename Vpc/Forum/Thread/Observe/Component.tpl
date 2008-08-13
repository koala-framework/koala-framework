<?php if($this->userIsAuthed) { ?>
    <div class="observeThread">
    <?php if ($this->isObserved) { ?>
        <a class="observed" href="<?= $this->observeUrl ?>">Thema beobachten</a>
    <?php } else { ?>
        <a class="notObserved" href="<?= $this->observeUrl ?>">Thema beobachten</a>
    <?php } ?>
    </div>
<?php } ?>
