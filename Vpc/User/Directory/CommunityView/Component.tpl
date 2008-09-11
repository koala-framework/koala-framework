<div class="<?=$this->cssClass?>">
    <? if (isset($this->searchForm)) echo $this->component($this->searchForm); ?>
    <? if (isset($this->paging)) echo $this->component($this->paging); ?>
    <ul>
        <?php foreach ($this->items as $item) { ?>
            <li>
                <?=$this->componentLink($item);?>
                <span class="rating"><?=$this->component($item->getChildComponent('-general')->getChildComponent('-rating'));?></span>
                <span class="memberSince">(<?=trlVps('Member since')?>:
                    <?=$this->date($item->row->created);?>)</span>
            </li>
        <?php } ?>
    </ul>
    <? if (isset($this->paging)) echo $this->component($this->paging); ?>
</div>