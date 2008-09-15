<div class="<?=$this->cssClass?>">
    <? if (isset($this->searchForm)) echo $this->component($this->searchForm); ?>
    <ul>
        <?php foreach ($this->items as $item) { ?>
            <li><?=$this->componentLink($item, method_exists($item->row->getRow(), 'getTreePath') ? $item->row->getRow()->getTreePath() : $item->row->__toString());?></li>
        <?php } ?>
    </ul>
</div>