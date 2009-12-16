<div class="<?=$this->cssClass?>">
    <?= $this->component($this->link) ?><?= $this->component($this->image) ?>
    <?=$this->ifHasContent($this->link)?>
    </a>
    <?=$this->ifHasContent()?>
</div>