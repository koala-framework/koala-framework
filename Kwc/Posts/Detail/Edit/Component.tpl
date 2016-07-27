<div class="<?=$this->rootElementClass?>">
    <?php if (!$this->isSaved) echo $this->component($this->preview); ?>
    <?=$this->component($this->form)?>
</div>
