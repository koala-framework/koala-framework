<?php if ($this->show) { ?>
<div class="<?=$this->cssClass?>">
    <span><?=$this->placeholders['prefix']?></span>
    <?php echo $this->partials($this->data, $this->partialParams)?>
</div>
<?php } ?>