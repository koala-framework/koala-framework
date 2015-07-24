<div class="kwfEyeCandyList <?=$this->rootElementClass?>">

    <?=$this->hiddenOptions($this->options)?>
    <div class="listSwitchLargeWrapper">
        <div class="listSwitchLargeContent"></div> <?/* this div is requred, see LargeContentPlugin */?>
        <div class="clear"></div>
    </div>

    <div class="listSwitchPreviewWrapper <?=$this->previewCssClass?>">
        <? $i = 0; ?>
        <? foreach ($this->listItems as $item) { ?>
            <div id="<?= $item['data']->componentId; ?>" class="listSwitchItem <?= $item['class']; ?>" style="<?=$item['style']?>">
                <?=$this->componentLink($item['largePage'], $this->component($item['data']), array('cssClass'=>'previewLink'))?>
            </div>
        <? } ?>
        <div class="clear"></div>
    </div>
</div>
