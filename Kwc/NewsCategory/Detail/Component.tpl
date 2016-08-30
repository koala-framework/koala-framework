<div class="<?=$this->rootElementClass?>">
    <h3><?= $this->title ?></h3>
    <div class="publishDate"><?=$this->date($this->row->publish_date)?>
        <?php if ($this->item->categories) { ?>
            | <?=$this->data->trlpKwf('Category', 'Categories', count($this->item->categories));?>:
            <?php $nci = 0;
            foreach ($this->item->categories as $nc) {
                if ($nci++ >= 1) echo ', ';
                echo $this->componentLink($nc);
            } ?>
        <?php } ?>
    </div>
    <div class="infoContainer"><?=$this->component($this->content) ?></div>
    <?php if ($this->placeholder['backLink']) { ?>
    <p class="back clear">
        <?=$this->componentLink($this->data->parent, '&laquo; '.$this->placeholder['backLink'])?>
    </p>
    <?php } ?>
</div>
