<div class="<?=$this->rootElementClass?>">
    <?= $this->component($this->link) ?><?= $this->component($this->image) ?>
    <?php if ($this->hasContent($this->link)) { ?>
    </a>
    <?php } ?>
</div>
