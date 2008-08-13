<div class="<?=$this->cssClass?>">
    <? if (isset($this->paging)) echo $this->component($this->paging); ?>
    <ul>
        <?php foreach ($this->items as $item) { ?>
            <li><?=$this->componentLink($item);?></li>
        <?php } ?>
    </ul>
    <? if (isset($this->paging)) echo $this->component($this->paging); ?>
</div>