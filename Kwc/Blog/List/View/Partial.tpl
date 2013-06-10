<a name="<?=$this->item->componentId;?>"></a>
<div class="entry <?=$this->dynamic('FirstLast')?>">
    <h2>
        <? if ($this->hasContent($this->item)) { ?>
            <?=$this->componentLink($this->item)?>
        <? } else { ?>
            <?=$this->item->row->title?>
        <? } ?>
    </h2>
    <div class="author">
        <p><?=$this->item->row->author_firstname;?> <?=$this->item->row->author_lastname;?></p>
    </div>
    <div class="publishDate">
        <p><?=$this->date($this->item->publish_date);?></p>
    </div>
    <div class="blogpost">
        <?=$this->component($this->blogpost)?>
    </div>
    <? if($this->hasContent($this->item) && $this->placeholder['readMore']) { ?>
        <div class="readMoreLink">
            <?=$this->componentLink($this->item, $this->placeholder['readMore']);?>
        </div>
    <? } ?>
    <div class="clear"></div>
</div>
