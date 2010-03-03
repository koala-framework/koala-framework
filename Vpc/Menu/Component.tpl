<? if (count($this->menu)) { ?>
    <div class="<?=$this->cssClass;?>">
        <? if ($this->parentPage) { ?>
            <h2 class="parentPageName"><?=$this->parentPage->name;?></h2>
        <? } ?>
        <ul class="menu">
            <? foreach ($this->menu as $i=>$m) { ?>
                <li class="<?=$m->class;?>">
                    <?=$this->componentLink($m, $this->linkPrefix.$m->name);?>
                    <? if($i < count($this->menu)-1) { ?><?=$this->separator;?><? } ?>
                    <? if (isset($this->subMenu) && isset($m->current) && $m->current) { ?>
                        <?=$this->component($this->subMenu);?>
                    <? } ?>
                </li>
            <? } ?>
        </ul>
        <div class="clear"></div>
    </div>
<? } ?>
