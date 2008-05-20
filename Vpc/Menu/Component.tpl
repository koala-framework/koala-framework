<ul id="<?= $this->level ?>Menu">
    <?php foreach ($this->menu as $m) { ?>
    <li class="<?= $m['class'] ?>">
        <?=$this->componentLink($m)?>
    </li>
    <?php } ?>
</ul>