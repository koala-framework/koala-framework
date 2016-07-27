<?php if ($this->view) { ?>
    <div class="<?=$this->rootElementClass?>">
        <?= $this->component($this->view); ?>
    </div>
<?php } ?>
