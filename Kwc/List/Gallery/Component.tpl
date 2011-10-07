<div class="<?=$this->cssClass;?> vpsEnlargeNextPrevious">
    <? $i = 0; ?>
    <? foreach ($this->children as $child) { ?>
        <?
            $class = 'listItem ';
            if ($i == 0) $class .= 'vpcFirst ';
            if ($i == count($this->children)-1) $class .= 'vpcLast ';
            if ($i % 2 == 0) {
                $class .= 'vpcEven ';
            } else {
                $class .= 'vpcOdd ';
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
