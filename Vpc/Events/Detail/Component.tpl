<div class="<?=$this->cssClass?>">
    <h3><?= $this->title ?></h3>
    <div class="date">
        <? if ($this->date($this->row->start_date,'H:i') != '00:00') { ?>
            <?=$this->dateTime($this->row->start_date);?>
        <? } else { ?>
            <?=$this->date($this->row->start_date);?>
        <? } ?>
        <? if ($this->row->end_date) { ?>
            <? if($this->date($this->row->end_date,'H:i') != '00:00') { ?>
                <?=$this->dateTime($this->row->end_date);?>
            <? } else { ?>
                <?=$this->date($this->row->end_date);?>
            <? } ?>
        <? } ?>
    </div>
    <div class="infoContainer"><?=$this->component($this->content) ?></div>
    <? if ($this->placeholder['backLink']) { ?>
    <p class="back clear">
        <?=$this->componentLink($this->data->parent, '&laquo; '.$this->placeholder['backLink'])?>
    </p>
    <? } ?>
</div>
