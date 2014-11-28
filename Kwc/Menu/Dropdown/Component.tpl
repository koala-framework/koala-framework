<?php if (count($this->menu)) { ?>
    <div class="<?=$this->cssClass;?>">
        <input type="hidden" value="<?=htmlspecialchars(json_encode($this->config))?>" />
        <ul class="menu">
            <? foreach ($this->menu as $m) { ?>
                <li class="<?=$m['class'];?>">
                    <?=$this->componentLink($m['data']);?>
                    <? if (isset($m['submenu']) && sizeof($m['submenu'])) { ?>
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
        </ul>
    </div>
<? } ?>