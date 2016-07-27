<div class="<?=$this->rootElementClass?>">
    <?php if (isset($this->searchForm)) echo $this->component($this->searchForm); ?>
    <div class="kwfUp-clear"></div>
    <?php if (isset($this->paging)) echo $this->component($this->paging); ?>
    <ul>
    <?=$this->partials($this->data)?>
    </ul>
    <?php if (isset($this->paging)) echo $this->component($this->paging); ?>
</div>
