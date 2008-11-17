<div class="<?=$this->cssClass?>">
    <? if (isset($this->searchForm)) echo $this->component($this->searchForm); ?>
    <? if (isset($this->paging)) echo $this->component($this->paging); ?>
    <div class="clear"></div>
    <ul>
        <?php foreach ($this->items as $item) { ?>
            <li class="thread">
                <?=$this->component($item->preview)?>
            </li>
        <?php } ?>
    </ul>
    <? if (isset($this->paging)) echo $this->component($this->paging); ?>
</div>