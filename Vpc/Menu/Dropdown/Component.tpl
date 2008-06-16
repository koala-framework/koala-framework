<ul id="<?=$this->level?>Menu">
    <?php foreach ($this->menu as $m) { ?>
    <li class="<?= $m['class'] ?>">
        <?=$this->componentLink($m)?>
        <?php if (isset($m['submenu'])) { ?>
            <div class="<?= $this->level ?>Submenu">
            <ul>
            <?php foreach ($m['submenu'] as $sm) { ?>
                <li class="<?= $sm['class'] ?>">
                <?=$this->componentLink($sm)?>
                </li>
            <?php } ?>
            </ul>
            </div>
        <?php } ?>
    </li>
    <?php } ?>
</ul>