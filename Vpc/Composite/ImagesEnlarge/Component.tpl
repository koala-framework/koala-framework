<div class="<?=$this->cssClass?>">
    <?php foreach ($this->children as $child) { ?>
        <div class="thumbOuter">
            <div class="thumb" style="width:<?= $this->thumbMaxWidth ?>px; height:<?= $this->thumbMaxHeight ?>px;">
                <?= $this->component($child) ?>
            </div>
        </div>
    <?php } ?>
    <div class="clear"></div>
</div>