<div class="<?=$this->rootElementClass?>" data-width="100%">
    <?php if ($this->image) { ?>
        <div class="<?=$this->bemClass('image')?>"><?=$this->component($this->image)?></div>
    <?php } ?>
    <div class="<?=$this->bemClass('text')?>">
        <?=$this->component($this->text)?>
    </div>
</div>
