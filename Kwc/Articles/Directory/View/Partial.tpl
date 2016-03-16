<article class="item">
    <div class="previewImage">
        <?=$this->componentLink($this->item, $this->component($this->item->getChildComponent('-previewImage')))?>
    </div>
    <div class="previewCenter">
        <div class="date"><?=$this->date($this->item->date)?>:</div>
        <h3><?=$this->componentLink($this->item, $this->item->title)?></h3>
        <div class="kwfUp-clear"></div>
        <div class="teaser">
            <?=$this->item->teaser?>
            <?=$this->componentLink($this->item, 'weiterlesen')?>
        </div>
    </div>
    <div class="kwfUp-clear"></div>
</article>
