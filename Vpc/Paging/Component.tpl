<?php if ($this->show) { ?>
<div class="<?=$this->cssClass?>">
    <span><?= trlVps('Page') ?>:</span>
    <?php echo $this->partials($this->data, 'Vps_Component_Partial_Pager', $this->partialParams)?>
</div>
<?php } ?>