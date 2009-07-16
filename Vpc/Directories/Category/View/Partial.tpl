<? if (!$this->hideCategoriesWithoutEntries || $this->item->listCount) { ?>
    <?=$this->componentLink($this->item, $this->placeholder['linkPrefix'].$this->item->getPage()->name.'<span class="counter">('.$this->item->listCount.')</span>');?>
<? } ?>