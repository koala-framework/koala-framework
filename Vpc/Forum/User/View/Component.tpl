<div class="<?=$this->cssClass?>">
    <? if (isset($this->paging)) echo $this->component($this->paging); ?>
    <ul>
        <?php foreach ($this->items as $item) { ?>
            <li>
                <?=$this->componentLink($item);?>
                <?php if ($item->row->created) { ?>
                    <span>( <?= trlVps('Member since') ?>: <?= $this->date($item->row->created) ?>)</span>
                <?php } ?>
            </li>
        <?php } ?>
    </ul>
    <? if (isset($this->paging)) echo $this->component($this->paging); ?>
</div>
