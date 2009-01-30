<div class="<?=$this->cssClass?>">
    <div class="<?=$this->propCssClass?>">
        <?=$this->component($this->text)?>
        <? if ($this->image) { ?>
            <?=$this->component($this->image)?>
        <? } ?>
    </div>
</div>