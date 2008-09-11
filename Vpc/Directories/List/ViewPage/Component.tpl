<div class="<?=$this->cssClass?>">
    <? if (isset($this->searchForm)) echo $this->component($this->searchForm); ?>
    <? if (isset($this->paging)) echo $this->component($this->paging); ?>
    <? if (!count($this->items)) { ?>
        <div class="noEntries"><?= $this->placeholder['noEntriesFound']; ?></div>
    <? } else { ?>
        <ul>
            <?php foreach ($this->items as $item) { ?>
                <li><?=$this->componentLink($item);?></li>
            <?php } ?>
        </ul>
    <? } ?>
    <? if (isset($this->paging)) echo $this->component($this->paging); ?>
</div>