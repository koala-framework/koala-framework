<a name="<?=$this->item->componentId;?>"></a>
<div class="entry <?=$this->dynamic('FirstLast')?>">
    <h2>
        <? if ($this->hasContent($this->item)) { ?>
            <?=$this->componentLink($this->item)?>
        <? } else { ?>
            <?=$this->item->row->title?>
        <? } ?>
    </h2>
    <div class="publishDate">
        <p><?=$this->date($this->item->publish_date);?></p>
    </div>
    <? if($this->showPreviewImage && $this->hasContent($this->item->previewImage)) { ?>
        <div class="prevImage">
            <?=$this->component($this->item->previewImage);?>
        </div>
    <? } ?>
    <? if($this->item->row->teaser) { ?>
        <div class="teaser">
            <p>
                <?=nl2br($this->item->row->teaser);?>
                <? if($this->hasContent($this->item) && $this->placeholder['readMore']) { ?>
                    <div class="readMoreLink">
                        <?=$this->componentLink($this->item, $this->placeholder['readMore']);?>
                    </div>
                <? } ?>
            </p>
        </div>
    <? } else if($this->hasContent($this->item) && $this->placeholder['readMore']) { ?>
        <div class="readMoreLink">
            <?=$this->componentLink($this->item, $this->placeholder['readMore']);?>
        </div>
    <? } ?>
    <div class="clear"></div>
</div>
