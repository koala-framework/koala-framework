<div class="<?=$this->cssClass?>">
    <?php foreach ($this->children as $child) { ?>
        <?php echo $this->component($child) ?>
    <?php } ?>
    <div class="clear"></div>
</div>
