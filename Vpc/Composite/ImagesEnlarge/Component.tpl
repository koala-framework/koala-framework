<div class="<?=$this->cssClass?>">
    <?php foreach ($this->children as $child) { ?>
        <div class="thumbOuter">
            <div class="thumb" style="width:<?= $this->smallMaxWidth; ?>px; height:<?= $this->smallMaxHeight; ?>px;">
                <?= $this->component($child) ?>
            </div>
        </div>
    <?php } ?>
    <div class="clear"></div>
    <!-- <a class="back" href="javascript:history.back()"><span>«</span>zurück</a> -->
</div>