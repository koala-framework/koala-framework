<div class="<?=$this->cssClass?>">
    <?php foreach ($this->children as $child) { ?>
        <div class="thumbOuter">
            <div class="thumb">
                <?= $this->component($child) ?>
            </div>
        </div>
    <?php } ?>
    <div class="clear"></div>
</div>