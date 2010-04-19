<?php if ($this->show) { ?>
<div class="<?=$this->cssClass?>">
    <span><?=$this->placeholders['prefix']?></span>
    <?php echo $this->partials($this->data, 'Vps_Component_Partial_Pager', $this->partialParams)?>
</div>
<?php } ?>