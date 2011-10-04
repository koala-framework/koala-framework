<? if (count($this->menu)) { ?>
    <div class="<?=$this->cssClass;?>">
        <? if ($this->parentPage) { ?>
            <h2 class="parentPageName"><?=$this->parentPage->name;?></h2>
        <? } ?>
        <ul class="menu">
            <? foreach ($this->menu as $m) { ?>
                <li class="<?=$m['class'];?>">
                    <? if (is_instance_of($m['data']->componentClass, 'Vpc_Basic_LinkTag_FirstChildPage_Component')) { ?>
                        <p><?=$m['text'];?></p>
                    <? } else { ?>
                        <?=$this->componentLink($m['data']);?>
                    <? } ?>
                    <? if (sizeof($m['submenu'])) { ?>
                        <ul class="subMenu">
                            <? foreach ($m['submenu'] as $sm) { ?>
                                <li class="<?=$sm['class'];?>">
                                    <?=$this->componentLink($sm['data'], $sm['text']);?>
                                </li>
                            <? } ?>
                        </ul>
                    <? } ?>
                </li>
            <? } ?>
        </ul>
        <div class="clear"></div>
    </div>
<? } ?>