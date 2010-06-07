<div class="text">
    <h2><?=$this->componentLink($this->item);?></h2>
    <span class="publishDate"><?=$this->date($this->item->row->start_date,'d.m.Y H:i')?></span>
    <? if($this->item->row->end_date) { ?>
        - 
        <span class="publishDate"><?=$this->date($this->item->row->end_date,'d.m.Y H:i')?></span>
    <? } ?>
    <p><?=$this->mailEncodeText($this->item->row->teaser)?></p>
</div>