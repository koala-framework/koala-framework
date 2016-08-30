<a name="<?=$this->item->componentId;?>"></a>
<div class="entry">
    <h1 class="title">
        <?php if ($this->hasContent($this->item)) { ?>
            <?=$this->componentLink($this->item)?>
        <?php } else { ?>
            <?=$this->item->row->title?>
        <?php } ?>
    </h1>
    <div class="postInfo">
        <?=$this->data->trlKwf('Posted on {0}', $this->date($this->item->publish_date))?>
        <?=$this->data->trlKwf('by {0}', $this->item->author)?>
    </div>
    <div class="content">
        <?=$this->component($this->content)?>
    </div>
    <?php if($this->hasContent($this->item) && $this->placeholder['readMore']) { ?>
        <div class="readMoreLink">
            <?=$this->componentLink($this->item, $this->placeholder['readMore']);?>
        </div>
    <?php } ?>
    <div class="categories">
        <?php if ($this->item->categories) { ?>
            <?=$this->data->trlKwf('This entry was posted in');?>
            <?php $nci = 0;
            foreach ($this->item->categories as $nc) {
                if ($nci++ >= 1) echo ', ';
                echo $this->componentLink($nc, null, array('skipAppendText'=>true));
            } ?>
        <?php } ?>
        <?=$this->data->trlKwf('by {0}', $this->item->author)?>
    </div>
    <div class="kwfUp-clear"></div>
</div>
