<div class="<?=$this->rootElementClass?>">
    <?php if (isset($this->paging)) echo $this->component($this->paging); ?>
    <?=$this->partials($this->data);?>
    <?php if (isset($this->paging)) echo $this->component($this->paging); ?>
</div>
