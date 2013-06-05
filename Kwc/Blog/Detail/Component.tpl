<div class="<?=$this->cssClass;?>">
    <h1><?=$this->title;?></h1>
    <div class="publishDate">
        <?=$this->date($this->row->publish_date);?>
    </div>
    <div class="author">
        <?=$this->author?>
    </div>
    <div class="infoContainer">
       <?=$this->component($this->content);?>
    </div>
    <div class="categories">
        <? if ($this->categories) { ?>
            | <?=$this->data->trlpKwf('Category', 'Categories', count($this->categories));?>:
            <? $nci = 0;
            foreach ($this->categories as $nc) {
                if ($nci++ >= 1) echo ', ';
                echo $this->componentLink($nc);
            } ?>
        <? } ?>
    </div>
    <? if ($this->placeholder['backLink']) { ?>
        <div class="backLink">
            <p><?=$this->componentLink($this->data->parent, '&laquo; '.$this->placeholder['backLink'])?><p>
        </div>
    <? } ?>
    <?=$this->component($this->comments)?>
</div>
