<a name="<?= $this->item->componentId; ?>"></a>
<div class="entry">
    <div class="text">
        <h3>
            <?=$this->ifHasContent($this->item)?>
                <?=$this->componentLink($this->item)?>
            <?=$this->ifHasContent()?>
            <?=$this->ifHasNoContent($this->item)?>
                <?=$this->item->row->title?>
            <?=$this->ifHasNoContent()?>
        </h3>
    </div>

    <div class="publishDate"><?=$this->date($this->item->publish_date)?></div>

    <div class="clear"></div>

    <div class="teaser">
        <p><?=$this->mailEncodeText($this->item->row->teaser)?></p>
        <p class="readMore">
            <?=$this->ifHasContent($this->item)?>
                <?=$this->componentLink($this->item, $this->item->trlVps('Read more') . ' &#xBB;')?>
            <?=$this->ifHasContent()?>
        </p>
    </div>
    <div class="clear"></div>
</div>
