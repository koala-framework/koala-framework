<div class="<?=$this->rootElementClass?>">
    <?php if ($this->write) { ?>
        <div class="write">
            <?=$this->componentLink($this->write, $this->placeholder['writeText'])?>
        </div>
    <?php } ?>

    <div class="comments">
        <?=$this->component($this->view)?>
    </div>

    <?php if ($this->quickwrite) { ?>
        <div class="quickwrite">
            <?=$this->component($this->quickwrite)?>
        </div>
    <?php } ?>
</div>
