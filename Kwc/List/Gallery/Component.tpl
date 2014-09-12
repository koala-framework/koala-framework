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
        <div class="<?=$class;?>">
            <?=$this->component($child);?>
        </div>
        <?
            $i++;
        ?>
    <? } ?>
</div>
