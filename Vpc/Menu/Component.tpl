<ul id="<?= $this->level ?>Menu">
    <?php foreach ($this->menu as $m) { ?>
    <li class="<?= $m['class'] ?>">
        <a href="<?= $m['href'] ?>" rel="<?= $m['rel'] ?>"><span><?= $m['text'] ?></span></a>
        <div class="clear"></div>
    </li>
    <?php } ?>
</ul>