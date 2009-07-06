<div class="<?=$this->cssClass?>">
    <div class="<?=$this->propCssClass?>">
        <div class="<? if($this->row->flow){?>flow<?}else{?>noFlow<?}?>">
            <? if ($this->image) { ?>
                <div class="image"><?=$this->component($this->image)?></div>
            <? } ?>
            <div class="text"<? if(!$this->row->flow) {?> style="margin-<?=$this->position?>: <?=$this->imageWidth?>px"<?}?>>
            <?=$this->component($this->text)?>
            </div>
            <div class="clear"></div>
        </div>
    </div>
</div>