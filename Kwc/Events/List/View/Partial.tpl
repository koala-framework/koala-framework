<a name="<?=$this->item->componentId;?>"></a>
<div class="entry">
    <h2>
        <?php if ($this->hasContent($this->item)) { ?>
            <?=$this->componentLink($this->item);?>
        <?php } else { ?>
            <?=$this->item->row->title;?>
        <?php } ?>
    </h2>
    <div class="publishDate">
        <p>
        <?php
            if ($this->date($this->item->row->start_date,'H:i') != '00:00' && ($this->date($this->item->row->start_date,'H:i') != $this->date($this->item->row->end_date,'H:i'))) {
                //startdatum mit unterschiedlicher uhrzeit
                echo $this->dateTime($this->item->row->start_date);
            } else {
                //startdatum mit gleicher uhrzeit
                echo $this->date($this->item->row->start_date);
            }
            if ($this->date($this->item->row->end_date)) {
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
            }
            //place
            if ($this->item->row->place) {
                echo " | ".$this->item->row->place;
            }
        ?>
        </p>
    </div>
    <?php if ($this->item->row->teaser) { ?>
        <div class="teaser">
            <p>
                <?=nl2br($this->item->row->teaser);?>
                <?php if ($this->hasContent($this->item) && $this->placeholder['readMore']) { ?>
                    <div class="readMoreLink">
                        <?=$this->componentLink($this->item, $this->placeholder['readMore']);?>
                    </div>
                <?php } ?>
            </p>
        </div>
    <?php } else if ($this->hasContent($this->item) && $this->placeholder['readMore']) { ?>
        <div class="readMoreLink">
            <?=$this->componentLink($this->item, $this->placeholder['readMore']);?>
        </div>
    <?php } ?>
</div>
