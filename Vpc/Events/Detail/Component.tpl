<div class="<?=$this->cssClass;?>">
    <h1><?=$this->title;?></h1>
    <div class="publishDate">
        <?
        if ($this->date($this->row->start_date,'H:i') != '00:00') {
            echo $this->dateTime($this->row->start_date);
        } else {
            echo $this->date($this->row->start_date);
        }
        if ($this->row->end_date && ($this->row->start_date!=$this->row->end_date)) {
            echo " - ";
            if($this->date($this->row->end_date,'H:i') != '00:00') {
                if($this->date($this->row->start_date,'H:i') != $this->date($this->row->end_date,'H:i')) {
                    echo $this->date($this->row->end_date, 'H:i');
                } else {
                    echo $this->dateTime($this->row->end_date);
                }
            } else {
                echo $this->date($this->row->end_date);
            }
        }
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
