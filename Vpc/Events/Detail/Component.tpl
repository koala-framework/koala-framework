<div class="<?=$this->cssClass;?>">
    <h1><?=$this->title;?></h1>
    <div class="publishDate">
    	
        <?
        if ($this->date($this->row->start_date,'H:i') != '00:00' && ($this->date($this->row->start_date,'H:i') != $this->date($this->row->end_date,'H:i'))) {
            //startdatum mit unterschiedlicher uhrzeit
            echo $this->dateTime($this->row->start_date);
        } else {
            //startdatum mit gleicher uhrzeit
            echo $this->date($this->row->start_date);
        }
        if ($this->date($this->row->start_date) != $this->date($this->row->end_date)) {
            //datum unterschiedlich
            if ($this->date($this->row->end_date,'H:i') != '00:00' && ($this->date($this->row->start_date,'H:i') != $this->date($this->row->end_date,'H:i'))) {
                //enddatum mit unterschiedlicher uhrzeit
                echo " - ".$this->dateTime($this->row->end_date);
            } else {
                //enddatum mit gleicher uhrzeit
                echo " - ".$this->date($this->row->end_date);
            }
        } else {
         //datum gleich
            if ($this->date($this->row->end_date,'H:i') != '00:00' && ($this->date($this->row->start_date,'H:i') != $this->date($this->row->end_date,'H:i'))) {
                //enddatum mit unterschiedlicher uhrzeit
                echo " - ".$this->dateTime($this->row->end_date,'H:i');
            }
        }
        //place
        if ($this->row->place) {
            echo " | ".$this->row->place;
        }
        ?>
    </div>
    <div class="infoContainer"><?=$this->component($this->content) ?></div>
    <? if ($this->placeholder['backLink']) { ?>
	    <div class="backLink">
	        <p><?=$this->componentLink($this->data->parent, '&laquo; '.$this->placeholder['backLink'])?><p>
	    </div>
    <? } ?>
</div>
