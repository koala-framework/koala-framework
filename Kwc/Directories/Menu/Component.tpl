<div class="<?=$this->rootElementClass?>">
    <?php foreach ($this->categories as $type=>$links) { ?>
    <h3><?=$type?></h3>
    <ul>
        <?php foreach ($links as $m) { ?>
            <li class="<?= $m['class'] ?>">
                <?=$this->componentLink($m['data'])?>
            </li>
        <?php } ?>
    </ul>
    <?php } ?>
</div>
