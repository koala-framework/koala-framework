<div class="<?=$this->rootElementClass?>" id="<?=$this->data->componentId?>">
    <?=$this->hiddenOptions($this->config)?>

    <?php if ($this->linkType == 'graphical') { ?>

    <div class="link switchLink">
        <a href="#"></a>
    </div>
    <div class="switchContent">
        <?=$this->favouriteText?>
    </div>

    <?php } else { ?>

    <div class="link switchLink">
        <a href="#" class="switchContent"><?=$this->favouriteText?></a>
    </div>

    <?php } ?>

</div>
