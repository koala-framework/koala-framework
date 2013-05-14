<div class="<?=$this->cssClass?>">
    <?=$this->hiddenOptions($this->config)?>

    <? if ($this->linkType == 'graphical') { ?>

    <div class="link switchLink">
        <a href="#"></a>
    </div>
    <div class="switchContent">
        <?=$this->favouriteText?>
    </div>

    <? } else { ?>

    <div class="link switchLink">
        <a href="#" class="switchContent"><?=$this->favouriteText?></a>
    </div>

    <? } ?>

</div>
