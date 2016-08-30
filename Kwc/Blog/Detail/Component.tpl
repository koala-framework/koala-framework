<div class="<?=$this->rootElementClass?>">
    <div class="blogContent">
        <?php if ($this->previousPost || $this->nextPost) { ?>
            <p class="nextPreviousLinks">
                <?php if ($this->previousPost) { ?>
                    <?=$this->componentLink($this->previousPost, $this->placeholder['previousLink'])?>
                <?php } ?>
                <?php if ($this->nextPost) { ?>
                    <?=$this->componentLink($this->nextPost, $this->placeholder['nextLink'])?>
                <?php } ?>
            </p>
        <?php } ?>
        <h1 class="title"><?=$this->componentLink($this->item, $this->title)?></h1>
        <div class="postInfo">
            <?=$this->data->trlKwf('Posted on {0}', $this->date($this->row->publish_date))?>
            <span class="author"><?=$this->data->trlKwf('by {0}', $this->item->author)?></span>
        </div>
        <div class="content">
            <?=$this->component($this->content);?>
        </div>
        <div class="categories">
            <?php if ($this->item->categories) { ?>
                <?=$this->data->trlKwf('This entry was posted in');?>
                <?php $nci = 0;
                foreach ($this->item->categories as $nc) {
                    if ($nci++ >= 1) echo ', ';
                    echo $this->componentLink($nc, null, array('skipAppendText'=>true));
                } ?>
            <?php } ?>
            <?=$this->data->trlKwf('by {0}', $this->item->author)?>.
            <?=$this->data->trlKwf('Bookmark the {0}.', $this->componentLink($this->data, $this->data->trlkwf('permalink')))?>
        </div>
        <?php if ($this->placeholder['backLink']) { ?>
            <div class="backLink">
                <p><?=$this->componentLink($this->data->parent, '&laquo; '.$this->placeholder['backLink'])?></p>
            </div>
        <?php } ?>
    </div>
    <div class="comments">
        <?=$this->component($this->comments)?>
    </div>
</div>
