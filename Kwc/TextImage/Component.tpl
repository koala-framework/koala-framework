<div class="<?=$this->cssClass?>" data-width="100%">
    <div class="<?=$this->propCssClass?>">
        <div class="<? if($this->center){ echo $this->position; }?><? if($this->row->flow){?> flow<?}else{?> noFlow<?}?>
        <? if(($this->imageWidth) <= 100) {?>smallImage<? } ?>">
            <? if ($this->image) { ?>
                <div class="image"><?=$this->component($this->image)?></div>
            <? } ?>
            <div class="text">
            <?=$this->component($this->text)?>
            </div>
            <div class="clear"></div>
        </div>
    </div>
</div>
