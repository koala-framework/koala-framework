<div class="<?=$this->cssClass?> vpsEnlargeNextPrevious">
    <?php foreach ($this->children as $child) { ?>
        <div class="thumbOuter">
            <div class="thumb" style="width:<?= $this->smallMaxWidth; ?>px; height:<?= $this->smallMaxHeight; ?>px;">
                <?= $this->component($child) ?>
            </div>
        </div>
    <?php } ?>
    <div class="clear"></div>
</div>