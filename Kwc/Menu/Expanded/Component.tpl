<? if (count($this->menu)) { ?>
    <div class="<?=$this->cssClass;?>">
        <? if ($this->parentPage) { ?>
            <h2 class="parentPageName"><?=$this->parentPage->name;?></h2>
        <? } ?>
        <ul class="menu">
            <? foreach ($this->menu as $m) { ?>
                <?=$m['preHtml']?>
                <li class="<?=$m['class'];?>">
                    <? if (is_instance_of($m['data'], 'Kwc_Basic_LinkTag_FirstChildPage_Data')) { ?>
                        <p><?=$m['text'];?></p>
                    <? } else { ?>
                        <?=$this->componentLink($m['data']);?>
                    <? } ?>
                    <? if (sizeof($m['submenu'])) { ?>
                        <ul class="subMenu">
                            <? foreach ($m['submenu'] as $sm) { ?>
                                <?=$sm['preHtml']?>
                                <li class="<?=$sm['class'];?>">
                                    <?=$this->componentLink($sm['data'], $sm['text']);?>
                                </li>
                                <?=$sm['postHtml']?>
                            <? } ?>
                        </ul>
                    <? } ?>
                </li>
                <?=$m['postHtml']?>
            <? } ?>
        </ul>
        <div class="clear"></div>
    </div>
<? } ?>