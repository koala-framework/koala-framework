<div class="<?=$this->rootElementClass?> kwfUp-kwfSwitchDisplay">
    <a class="<?=$this->bemClass('switchLink')?> kwfUp-linktext kwfUp-switchLink" href="#"><?=$this->component($this->linktext)?></a>
    <?php $class = ''; ?>
    <?php if ($this->startOpened) { ?>
        <?php $class = ' kwfUp-active'; ?>
    <?php } ?>
    <div class="<?=$this->bemClass('content')?> kwfUp-switchContent<?=$class?>">
        <?=$this->component($this->content)?>
    </div>
</div>
