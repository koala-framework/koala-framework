<a name="<?=$this->item->componentId;?>"></a>
<div class="entry <?=$this->dynamic('FirstLast')?>">
    <h1 class="title">
        <? if ($this->hasContent($this->item)) { ?>
            <?=$this->componentLink($this->item)?>
        <? } else { ?>
            <?=$this->item->row->title?>
        <? } ?>
    </h1>
    <div class="postInfo">
        <?=$this->data->trlKwf('Posted on {0}', $this->date($this->item->publish_date))?>
        <?=$this->data->trlKwf('by {0}', $this->item->author)?>
    </div>
    <div class="content">
        <?=$this->component($this->content)?>
    </div>
    <? if($this->hasContent($this->item) && $this->placeholder['readMore']) { ?>
        <div class="readMoreLink">
            <?=$this->componentLink($this->item, $this->placeholder['readMore']);?>
        </div>
    <? } ?>
    <div class="categories">
        <? if ($this->item->categories) { ?>
            <?=$this->data->trlKwf('This entry was posted in');?>
            <? $nci = 0;
            foreach ($this->item->categories as $nc) {
                if ($nci++ >= 1) echo ', ';
                echo $this->componentLink($nc, null, array('skipAppendText'=>true));
            } ?>
        <? } ?>
        <?=$this->data->trlKwf('by {0}', $this->item->author)?>
    </div>
    <div class="clear"></div>
</div>
