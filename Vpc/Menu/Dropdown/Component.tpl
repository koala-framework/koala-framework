<?php if (count($this->menu)) { ?>
    <div class="<?=$this->cssClass;?>">
        <ul class="menu">
            <? foreach ($this->menu as $m) { ?>
                <li class="<?=$m['class'];?>">
                    <?=$this->componentLink($m['data']);?>
                    <? if (sizeof($m['submenu'])) { ?>
                        <div class="dropdown">
                            <ul>
                                <? foreach ($m['submenu'] as $sm) { ?>
                                    <li class="<?=$sm['class'];?>">
                                        <?=$this->componentLink($sm['data']);?>
                                    </li>
                                <? } ?>
                            </ul>
                        </div>
                    <? } ?>
                </li>
            <? } ?>
            <div class="clear"></div>
        </ul>
    </div>
<? } ?>