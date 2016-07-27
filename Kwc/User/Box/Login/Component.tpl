<div class="<?=$this->rootElementClass?>">
    <?=$this->component($this->form)?>
    <?php if ($this->facebook) { ?>
        <?=$this->component($this->facebook)?>.
    <?php } ?>
</div>
