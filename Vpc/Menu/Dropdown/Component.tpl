<? if (count($this->menu)) { ?>
<ul class="<?=$this->cssClass?>">
    <?php foreach ($this->menu as $m) { ?>
    <li class="<?= $m->class ?>">
        <?=$this->componentLink($m)?>
        <?php if (sizeof($m->submenu)) { ?>
            <div class="dropdown">
            <ul>
            <?php foreach ($m->submenu as $sm) { ?>
                <li class="<?= $sm->class ?>">
                <?=$this->componentLink($sm, str_replace(' ', '&nbsp;', $sm->name))?>
                </li>
            <?php } ?>
            </ul>
            </div>
        <?php } ?>
    </li>
    <?php } ?>
</ul>
<? } ?>