<ul id="{$component.level}Menu">
    <?php foreach ($this->menu as $m) { ?>
    <li class="<?= $m['class'] ?>">
        <a href="<?= $m['href'] ?>" rel="<?= $m['rel'] ?>"><span><?= $m['text'] ?></span></a>
        <div class="clear"></div>
        <?php if (isset($m['submenu'])) { ?>
            <div class="<?= $this->level ?>Submenu">
            <ul>
            <?php foreach ($m['submenu'] as $sm) { ?>
                <li class="<?= $sm['class'] ?>">
                <a href="<?= $sm['href'] ?>" rel="<?= $sm['rel'] ?>"><span><?= $sm['text'] ?></span></a>
                </li>
            <?php } ?>
            </ul>
            </div>
        <?php } ?>
    </li>
    <?php } ?>
</ul>