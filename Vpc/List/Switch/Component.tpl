<div class="vpsListSwitch <?=$this->cssClass?>">
    <input type="hidden" class="options" value="<?= htmlspecialchars(Zend_Json::encode($this->options)) ?>" />
    <div class="listSwitchLargeWrapper">
        <div class="listSwitchLargeContent">
            <?=$this->component($this->items[0]['large']);/*wg. Flackern */?>
        </div>
        <a href="#" class="listSwitchPrevious"><?=$this->placeholder['prev'];?></a>
        <a href="#" class="listSwitchNext"><?=$this->placeholder['next'];?></a>
        <div class="clear"></div>
    </div>

    <div class="listSwitchPreviewWrapper <?=$this->previewCssClass?>">
        <? foreach ($this->items as $item) {
            ?><div class="listSwitchItem <?= $item['class']; ?>">
                <a href="#" class="previewLink"><?=$this->component($item['preview']);?></a>
                <div class="largeContent"><?= $this->component($item['large']); ?></div>
            </div><?
        } ?>
        <div class="clear"></div>
    </div>
</div>
