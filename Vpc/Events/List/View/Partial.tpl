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
            if ($this->date($this->item->row->start_date,'H:i') != '00:00' && ($this->date($this->item->row->start_date,'H:i') != $this->date($this->item->row->end_date,'H:i'))) {
			    //startdatum mit unterschiedlicher uhrzeit
                echo $this->dateTime($this->item->row->start_date);
            } else {
                //startdatum mit gleicher uhrzeit
                echo $this->date($this->item->row->start_date);
            }
			if ($this->date($this->item->row->start_date) != $this->date($this->item->row->end_date)) {
                //datum unterschiedlich
	            if ($this->date($this->item->row->end_date,'H:i') != '00:00' && ($this->date($this->item->row->start_date,'H:i') != $this->date($this->item->row->end_date,'H:i'))) {
                    //enddatum mit unterschiedlicher uhrzeit
	                echo " - ".$this->dateTime($this->item->row->end_date);
	            } else {
				    //enddatum mit gleicher uhrzeit
	                echo " - ".$this->date($this->item->row->end_date);
	            }
			} else {
			 //datum gleich
                if ($this->date($this->item->row->end_date,'H:i') != '00:00' && ($this->date($this->item->row->start_date,'H:i') != $this->date($this->item->row->end_date,'H:i'))) {
                    //enddatum mit unterschiedlicher uhrzeit
                    echo " - ".$this->dateTime($this->item->row->end_date,'H:i');
                }
			}
			//place
            if ($this->item->row->place) {
                echo " | ".$this->item->row->place;
            }
			?>
        </p>
    </div>
    <? if($this->item->row->teaser) { ?>
        <div class="teaser">
            <p>
                <?=nl2br($this->item->row->teaser);?>
                <? if($this->hasContent($this->item)) { ?>
                    <span class="readMoreLink">
                        <?=$this->componentLink($this->item, $this->item->trlVps('Read more').' &raquo;');?>
                    </span>
                <? } ?>
            </p>
        </div>
    <? } else if($this->hasContent($this->item)) { ?>
        <div class="readMoreLink">
            <?=$this->componentLink($this->item, $this->item->trlVps('Read more').' &raquo;');?>
        </div>
    <? } ?>
</div>
