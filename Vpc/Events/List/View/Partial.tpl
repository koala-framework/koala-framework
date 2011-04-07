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
        	<?
			if ($this->date($this->item->row->start_date,'H:i') != '00:00') {
                echo $this->dateTime($this->item->row->start_date);
            } else {
                echo $this->date($this->item->row->start_date);
            }
            if ($this->item->row->end_date && ($this->item->row->start_date!=$this->item->row->end_date)) {
                echo " - ";
                if($this->date($this->item->row->end_date,'H:i') != '00:00') {
                    if($this->date($this->item->row->start_date,'H:i') != $this->date($this->item->row->end_date,'H:i')) {
                        echo $this->date($this->item->row->end_date, 'H:i');
					} else {
                        echo $this->dateTime($this->item->row->end_date);
					}
                } else {
                    echo $this->date($this->item->row->end_date);
                }
            }
            if ($this->item->row->place) {
                echo " | ".$this->item->row->place;
            }
			?>
        </p>
    </div>
    <? if($this->item->row->teaser) { ?>
	    <div class="teaser">
	        <p><?=nl2br($this->mailEncodeText($this->item->row->teaser));?></p>
	    </div>
    <? } ?>
    <? if($this->hasContent($this->item)) { ?>
        <div class="readMoreLink">
            <p><?=$this->componentLink($this->item, $this->item->trlVps('Read more') . ' &raquo;');?></p>
        </div>
    <? } ?>
</div>
