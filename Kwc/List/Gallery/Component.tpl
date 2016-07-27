<?=$this->component($this->downloadAll); ?>
<div class="<?=$this->rootElementClass?>" data-width="100%">
    <?php $i = 0; ?>
    <?php foreach ($this->children as $child) { ?>
        <?php
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
            if ($i >= $this->showPics && $this->showPics) {
                $class .= 'showMorePic ';
            }
            $class = trim($class);
        ?>
        <div class="<?=$class;?>">
            <?=$this->component($child);?>
        </div>
        <?php
            $i++;
        ?>
    <?php } ?>
    <?php if ($this->showPics && count($this->children) > $this->showPics) { ?>
        <div class="moreButton"><div class="innerMoreButton"><span><?=$this->moreButtonText;?></span></div></div>
    <?php } ?>
</div>
