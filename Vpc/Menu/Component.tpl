<? if (count($this->menu)) { ?>

<? if ($this->parentPage) { ?>
<h2><?=$this->parentPage->name?></h2>
<? } ?>

<ul class="<?=$this->cssClass?>">
    <?php foreach ($this->menu as $i=>$m) { ?>
    <li class="<?= $m->class ?>">
        <?=$this->componentLink($m, $this->linkPrefix.$m->name)?>
        <? if($i < count($this->menu)-1) { ?><?=$this->separator?><? } ?>
        <? if (isset($this->subMenu) && isset($m->current) && $m->current) { ?>
        <?=$this->component($this->subMenu)?>
        <? } ?>
    </li>
    <?php } ?>
    <div class="clear"></div>
</ul>
<? } ?>
