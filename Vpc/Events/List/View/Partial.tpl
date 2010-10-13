<a name="<?=$this->item->componentId;?>"></a>
<div class="entry">
    <div class="text">
        <h3>
            <?=$this->ifHasContent($this->item);?>
                <?=$this->componentLink($this->item);?>
            <?=$this->ifHasContent();?>
            <?=$this->ifHasNoContent($this->item);?>
                <?=$this->item->row->title?>
            <?=$this->ifHasNoContent();?>
        </h3>
        <div class="publishDate">

            <? if ($this->date($this->item->row->start_date,'H:i') != '00:00') { ?>
                <?=$this->date($this->item->row->start_date,'d.m.Y H:i');?>
            <? } else { ?>
                <?=$this->date($this->item->row->start_date,'d.m.Y');?>
            <? } ?>

            <? if ($this->item->row->end_date) { ?>
                -
                <? if($this->date($this->item->row->end_date,'H:i') != '00:00') { ?>
                    <?=$this->date($this->item->row->end_date,'d.m.Y H:i');?>
                <? } else { ?>
                    <?=$this->date($this->item->row->end_date,'d.m.Y');?>
                <? } ?>
            <? } ?>

            <? if ($this->item->row->place) { ?>
                |
                <?=$this->item->row->place;?>
            <? } ?>

        </div>
    </div>

    <div class="clear"></div>

    <div class="teaser">
        <p><?=nl2br($this->mailEncodeText($this->item->row->teaser));?></p>
        <p class="readMore">
            <?=$this->ifHasContent($this->item);?>
                <?=$this->componentLink($this->item, $this->item->trlVps('Read more') . ' &raquo;');?>
            <?=$this->ifHasContent();?>
        </p>
    </div>
    <div class="clear"></div>
</div>
