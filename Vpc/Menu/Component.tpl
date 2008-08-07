<? if (count($this->menu)) { ?>
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
</ul>
<? } ?>
