<div class="<?=$this->cssClass?>">
    <?= $this->component($this->link) ?><?= $this->component($this->image) ?>
    <?if ($this->hasContent($this->link)) {?>
    </a>
    <?}?>
</div>