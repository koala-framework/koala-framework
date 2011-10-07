<div class="<?=$this->cssClass?>">
    <div class="left firstColumn" style="width: <?=$this->leftWidth?>px;">
        <?=$this->component($this->leftColumn);?>
    </div>
    <div class="right secondColumn" style="width: <?=$this->rightWidth?>px;">
        <?=$this->component($this->rightColumn);?>
    </div>
    <div class="clear"></div>
</div>