<div class="<?=$this->cssClass?>">
<?=$this->component($this->paging)?>
<?php foreach ($this->items as $item) { ?>
    <div class="text">
        <?=$this->componentLink($item);?>
        <span class="publishDate"><?=$this->date($item->row->start_date)?></span>
        <span class="publishDate"><?=$this->date($item->row->end_date)?></span>
        <p><?=$this->mailEncodeText($item->row->teaser)?></p>
    </div>
<?php } ?>
<?=$this->component($this->paging)?>
</div>