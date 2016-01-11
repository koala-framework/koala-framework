<div class="<?=$this->rootElementClass?> kwfUp-kwfSwitchDisplay">
    <a class="<?=$this->bemClass('switchLink')?> kwfUp-linktext kwfUp-switchLink" href="#"><?=$this->component($this->linktext)?></a>
    <?$class = '';?>
    <? if ($this->startOpened) { ?>
        <?$class = ' kwfUp-active';?>
    <? } ?>
    <div class="<?=$this->bemClass('content')?> kwfUp-switchContent<?=$class?>">
        <?=$this->component($this->content)?>
    </div>
</div>
