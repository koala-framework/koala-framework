<li>
    <h3><?=$this->componentLink($this->item)?></h3>
    <?=$this->dateTime($this->item->row->publish_date)?>
    <p><?=$this->truncate($this->item->row->teaser)?></p>
</li>
