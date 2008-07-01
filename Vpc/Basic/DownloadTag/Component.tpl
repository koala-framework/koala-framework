<div class="<?=$this->cssClass?>">
    <?php if ($this->url) { ?>
    <a href="<?= $this->url ?>"><?php if ($this->icon) { ?><img src="<?= $this->icon ?>" /><?php } ?>
    <?php } ?>
</div>