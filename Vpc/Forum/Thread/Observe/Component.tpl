<?php if($this->userIsAuthed) { ?>
    <?php if ($this->isObserved) { ?>
        <a class="observed" href="<?= $this->observeUrl ?>">Thema beobachten</a>
    <?php } else { ?>
        <a class="notObserved" href="<?= $this->observeUrl ?>">Thema beobachten</a>
    <?php } ?>
<?php } ?>
