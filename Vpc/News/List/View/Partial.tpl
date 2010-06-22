<a name="<?=$this->item->componentId;?>"></a>
<div class="entry">
    <div class="text">
        <h3>
            <?if ($this->hasContent($this->item)) {?>
                <?=$this->componentLink($this->item)?>
            <?} else { ?>
                <?=$this->item->row->title?>
            <?}?>
        </h3>
    </div>

    <div class="publishDate"><?=$this->date($this->item->publish_date);?></div>

    <div class="clear"></div>

    <div class="teaser">
        <p><?=nl2br($this->mailEncodeText($this->item->row->teaser));?></p>
        <p class="readMore">
            <?if ($this->hasContent($this->item)) {?>
                <?=$this->componentLink($this->item, $this->item->trlVps('Read more') . ' &#xBB;')?>
            <?}?>
        </p>
    </div>
    <div class="clear"></div>
</div>
