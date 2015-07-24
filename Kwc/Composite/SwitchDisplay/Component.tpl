<div class="<?=$this->rootElementClass?> kwfSwitchDisplay">
    <a class="linktext switchLink" href="#"><?=$this->component($this->linktext)?></a>
    <?$class = '';?>
    <? if ($this->startOpened) { ?>
        <?$class = ' active';?>
    <? } ?>
    <div class="content switchContent<?=$class?>">
        <?=$this->component($this->content)?>
    </div>
</div>
