<?=$this->component($this->downloadAll); ?>
<div class="<?=$this->cssClass;?>" data-width="100%">
    <? $i = 0; ?>
    <? foreach ($this->children as $child) { ?>
        <?
            $class = 'listItem ';
            if ($i == 0) $class .= 'kwcFirst ';
            if ($i == count($this->children)-1) $class .= 'kwcLast ';
            if ($i % 2 == 0) {
                $class .= 'kwcEven ';
            } else {
                $class .= 'kwcOdd ';
            }
            if ($i%$this->imagesPerLine == $this->imagesPerLine-1) {
                $class .= 'lastInLine ';
            }
            if ($i%$this->imagesPerLine == 0) {
                $class .= 'firstInLine ';
            }
            $class = trim($class);
        ?>
        <? if ($i == $this->showPics && $this->showPics) { ?>
            <div class="morePics">
        <? } ?>
        <div class="<?=$class;?>">
            <?=$this->component($child);?>
        </div>
        <? if ($i == count($this->children)-1 && $this->showPics) { ?>
            </div>
        <? } ?>
        <?
            $i++;
        ?>
    <? } ?>
    <? if ($this->showPics && count($this->children) > $this->showPics) { ?>
        <div class="moreButton"><div class="innerMoreButton"><span><?=$this->placeholder['moreButton'];?></span></div></div>
    <? } ?>
</div>
