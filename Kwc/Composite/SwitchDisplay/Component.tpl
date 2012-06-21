<div class="<?=$this->cssClass?> kwfSwitchDisplay">
    <a class="linktext switchLink" href="#"><?=$this->component($this->linktext)?></a>
    <div class="content switchContent">
        <? if ($this->startOpened) { ?>
            <div class="kwfImportant"></div>
        <? } ?>
        <?=$this->component($this->content)?>
    </div>
</div>