<a name="<?= $this->item->componentId; ?>"></a>
<div class="entry">
    <div class="text">
        <h1><?=$this->componentLink($this->item)?></h1>
        <p>
            <span class="publishDate"><?=$this->date($this->item->publish_date)?>
                <?php
                if ($this->item->categories) { ?>
                    | <?= $this->data->trlpKwf('Category', 'Categories', count($this->item->categories)); ?>
                    <?php $nci = 0;
                    foreach ($this->item->categories as $nc) {
                        if ($nci++ >= 1) echo ', ';
                        echo $this->componentLink($nc);
                    } ?>
                <?php } ?>
            </span>
            <div class="kwfUp-clear"></div>
        </p>
    </div>
    <div class="kwfUp-clear"></div>
    <?php if (isset($this->item->previewImage)) { ?>
    <div class="image left">
        <?=$this->componentLink($this->item, $this->component($this->item->previewImage))?>
    </div>
    <?php } ?>
    <div class="teaser">
        <p><?=$this->item->row->teaser?></p>
        <?=$this->componentLink($this->item, trlKwf('Read more').' &raquo;');?>
    </div>
    <div class="kwfUp-clear"></div>
</div>
