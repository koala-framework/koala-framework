<div class="<?=$this->rootElementClass?>">
    <?= $this->component($this->link) ?><?= $this->component($this->image) ?>
    <?if ($this->hasContent($this->link)) {?>
    </a>
    <?}?>
</div>