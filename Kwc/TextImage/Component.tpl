<div class="<?=$this->rootElementClass?>" data-width="100%">
    <? if ($this->image) { ?>
        <div class="<?=$this->bemClass('image')?>"><?=$this->component($this->image)?></div>
    <? } ?>
    <div class="<?=$this->bemClass('text')?>">
        <?=$this->component($this->text)?>
    </div>
</div>
