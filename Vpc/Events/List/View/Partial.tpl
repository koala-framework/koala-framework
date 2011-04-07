<a name="<?=$this->item->componentId;?>"></a>
<div class="entry">
    <h2>
        <? if ($this->hasContent($this->item)) { ?>
            <?=$this->componentLink($this->item);?>
        <? } else { ?>
            <?=$this->item->row->title;?>
        <? } ?>
    </h2>
    <div class="publishDate">
        <p>
        	<? if ($this->date($this->item->row->start_date,'H:i') != '00:00') { ?>
                <?=$this->dateTime($this->item->row->start_date);?>
            <? } else { ?>
                <?=$this->date($this->item->row->start_date);?>
            <? } ?>

            <? if ($this->item->row->end_date) { ?>
                -
                <? if($this->date($this->item->row->end_date,'H:i') != '00:00') { ?>
                    <?=$this->dateTime($this->item->row->end_date);?>
                <? } else { ?>
                    <?=$this->date($this->item->row->end_date);?>
                <? } ?>
            <? } ?>

            <? if ($this->item->row->place) { ?>
                |
                <?=$this->item->row->place;?>
            <? } ?>
        </p>
    </div>
    <div class="teaser">
        <p><?=nl2br($this->mailEncodeText($this->item->row->teaser));?></p>
    </div>
    <? if($this->hasContent($this->item)) { ?>
        <div class="readMoreLink">
            <p><?=$this->componentLink($this->item, $this->item->trlVps('Read more') . ' &raquo;');?></p>
        </div>
    <? } ?>
</div>
