<div class="<?=$this->cssClass?>">
    <? if (isset($this->searchForm)) echo $this->component($this->searchForm); ?>
    <? if (isset($this->paging)) echo $this->component($this->paging); ?>
    <? if ($this->formSaved && !count($this->items)) { ?>
        <div class="noEntries"><?= $this->placeholder['noEntriesFound']; ?></div>
    <? } else { ?>
        <ul>
            <?php foreach ($this->items as $item) { ?>
                <li>
                    <? if ($item->row->getItem()->SmallImage) { ?>
                    <img src="<?=$item->row->getItem()->SmallImage->Url->__toString()?>" width="<?=$item->row->getItem()->SmallImage->Width?>" height="<?=$item->row->getItem()->SmallImage->Height?>" alt="" />
                    <? } ?>
                    <?=$this->componentLink($item);?>
                </li>
            <?php } ?>
        </ul>
    <? } ?>
    <? if (isset($this->paging)) echo $this->component($this->paging); ?>
</div>