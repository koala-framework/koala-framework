<div class="<?=$this->cssClass?>">
    <? if (isset($this->searchForm)) echo $this->component($this->searchForm); ?>
    <? if (isset($this->paging)) echo $this->component($this->paging); ?>
    <ul>
        <?php foreach ($this->items as $item) { ?>
            <li>
                <?=$this->componentLink($item);?>
                <?=$this->component($item->getChildComponent('-general')->getChildComponent('-rating'));?>
                (<?=trlVps('Member since')?>:
                    <?=$this->date($item->row->created);?>)
            </li>
        <?php } ?>
    </ul>
    <? if (isset($this->paging)) echo $this->component($this->paging); ?>
</div>