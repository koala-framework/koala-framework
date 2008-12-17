<div class="<?=$this->cssClass?>">
    <? if (isset($this->searchForm)) echo $this->component($this->searchForm); ?>
    <? if (isset($this->paging)) echo $this->component($this->paging); ?>
    <ul>
        <? foreach ($this->items as $item) { ?>
            <li>
                    <?=$this->componentLink($item, $this->placeholder['linkPrefix'].$item->getPage()->name.'<span class="counter">('.$item->listCount.')</span>');?>
            </li>
        <? } ?>
    </ul>
    <? if (isset($this->paging)) echo $this->component($this->paging); ?>
</div>