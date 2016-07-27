<div class="<?=$this->rootElementClass?>">
    <?php if (isset($this->searchForm)) echo $this->component($this->searchForm); ?>
    <?php if (isset($this->paging)) echo $this->component($this->paging); ?>
    <ul>
        <?=$this->partialsPaging($this->data);?>
    </ul>
    <?php if (isset($this->paging)) echo $this->component($this->paging); ?>
</div>
