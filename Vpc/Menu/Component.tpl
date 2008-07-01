<? if (count($this->menu)) { ?>
<ul class="<?=$this->cssClass?>">
    <?php foreach ($this->menu as $m) { ?>
    <li class="<?= $m->class ?>">
        <?=$this->componentLink($m)?>
        <? if (isset($this->subMenu) && isset($m->current) && $m->current) { ?>
        <?=$this->component($this->subMenu)?>
        <? } ?>
    </li>
    <?php } ?>
</ul>
<? } ?>