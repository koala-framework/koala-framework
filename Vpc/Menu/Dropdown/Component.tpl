<?php if (count($this->menu)) { ?>
<ul class="<?=$this->cssClass?>">
    <? foreach ($this->menu as $m) { ?>
        <li class="<?= $m->class ?>">
            <?=$this->componentLink($m)?>
            <? if (sizeof($m->submenu)) { ?>
                <div class="dropdown">
                    <ul>
                        <? foreach ($m->submenu as $sm) { ?>
                            <li class="<?= $sm->class ?>">
                                <?=$this->componentLink($sm, str_replace(' ', '&nbsp;', $sm->name))?>
                            </li>
                        <? } ?>
                    </ul>
                </div>
            <? } ?>
        </li>
    <? } ?>
</ul>
<? } ?>