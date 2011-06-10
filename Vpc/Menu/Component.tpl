<? if (count($this->menu)) { ?>
    <div class="<?=$this->cssClass;?>">
        <? if ($this->parentPageLink) { ?>
            <h2 class="parentPageName"><?=$this->componentLink($this->parentPage);?></h2>
        <? } else if ($this->parentPage) { ?>
            <h2 class="parentPageName"><?=$this->parentPage->name;?></h2>
        <? } ?>
        <ul class="menu">
            <? $i = 0;
            foreach ($this->menu as $m) { ?>
                <li class="<?=$m['class'];?>">
                    <?=$this->componentLink($m['data'], $this->linkPrefix.$m['text']);?>
                    <? if ($i < count($this->menu)-1) { ?><?=$this->separator;?><? } ?>
                    <? if (isset($this->subMenu) && isset($m['current']) && $m['current']) { ?>
                        <?=$this->component($this->subMenu);?>
                    <? } ?>
                </li>
            <? $i++;
            } ?>
        </ul>
        <div class="clear"></div>
    </div>
<? } ?>
