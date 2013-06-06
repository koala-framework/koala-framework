<?=$this->component($this->downloadAll); ?>
<div class="<?=$this->cssClass;?>">
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
            $class = trim($class);
        ?>
        <div class="<?=$class;?>">
            <?=$this->component($child);?>
        </div>
        <?
            if ($i%$this->imagesPerLine == $this->imagesPerLine-1) {
                echo '<div class="clear"></div>';
            }
            $i++;
        ?>
    <? } ?>
    <div class="clear"></div>
</div>
