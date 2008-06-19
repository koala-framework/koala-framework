<? if (count($this->menu)) { ?>
<ul class="<?=$this->cssClass?>">
    <?php foreach ($this->menu as $m) { ?>
    <li class="<?= $m->class ?>">
        <?=$this->componentLink($m)?>
    </li>
    <?php } ?>
</ul>
<? } ?>