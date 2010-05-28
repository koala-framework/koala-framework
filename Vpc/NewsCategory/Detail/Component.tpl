<div class="<?=$this->cssClass?>">
    <h3><?= $this->title ?></h3>
    <div class="publishDate"><?=$this->date($this->row->publish_date)?>
        <? if ($this->categories) { ?>
            | <?=$this->data->trlpVps('Category', 'Categories', count($this->categories));?>:
            <? $nci = 0;
            foreach ($this->categories as $nc) {
                if ($nci++ >= 1) echo ', ';
                echo $this->componentLink($nc);
            } ?>
        <? } ?>
    </div>
    <div class="infoContainer"><?=$this->component($this->content) ?></div>
    <? if ($this->placeholder['backLink']) { ?>
    <p class="back clear">
        <?=$this->componentLink($this->data->parent, '&laquo; '.$this->placeholder['backLink'])?>
    </p>
    <? } ?>
</div>
