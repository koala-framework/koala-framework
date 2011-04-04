<div class="<?=$this->cssClass?>">
    <h3><?= $this->title ?></h3>
    <div class="publishDate"><?=$this->date($this->row->publish_date)?></div>
    <div class="infoContainer"><?=$this->component($this->content) ?></div>
    <? if ($this->placeholder['backLink']) { ?>
    <p class="back clear">
        <?=$this->componentLink($this->data->parent, '&laquo; '.$this->placeholder['backLink'])?>
    </p>
    <? } ?>
</div>
