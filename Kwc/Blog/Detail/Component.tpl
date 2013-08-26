<div class="<?=$this->cssClass;?>">
    <div class="blogContent">
        <? if ($this->previousPost || $this->nextPost) { ?>
            <p class="nextPreviousLinks">
                <? if ($this->previousPost) { ?>
                    <?=$this->componentLink($this->previousPost, $this->placeholder['previousLink'])?>
                <? } ?>
                <? if ($this->nextPost) { ?>
                    <?=$this->componentLink($this->nextPost, $this->placeholder['nextLink'])?>
                <? } ?>
            </p>
        <? } ?>
        <h1 class="title"><?=$this->componentLink($this->item, $this->title)?></h1>
        <div class="postInfo">
            <?=$this->data->trlKwf('Posted on {0}', $this->date($this->row->publish_date))?>
            <?=$this->data->trlKwf('by {0}', $this->item->author)?>
        </div>
        <div class="content">
            <?=$this->component($this->content);?>
        </div>
        <div class="categories">
            <? if ($this->item->categories) { ?>
                <?=$this->data->trlKwf('This entry was posted in');?>
                <? $nci = 0;
                foreach ($this->item->categories as $nc) {
                    if ($nci++ >= 1) echo ', ';
                    echo $this->componentLink($nc, null, array('skipAppendText'=>true));
                } ?>
            <? } ?>
            <?=$this->data->trlKwf('by {0}', $this->item->author)?>.
            <?=$this->data->trlKwf('Bookmark the {0}.', $this->componentLink($this->data, $this->data->trlkwf('permalink')))?>
        </div>
        <? if ($this->placeholder['backLink']) { ?>
            <div class="backLink">
                <p><?=$this->componentLink($this->data->parent, '&laquo; '.$this->placeholder['backLink'])?></p>
            </div>
        <? } ?>
    </div>
    <div class="comments">
        <?=$this->component($this->comments)?>
    </div>
</div>
