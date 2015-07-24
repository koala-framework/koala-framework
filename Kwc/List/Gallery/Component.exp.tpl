<?=$this->component($this->downloadAll); ?>
<div class="<?=$this->rootElementClass?>">
    <? $i = 0; ?>
    <? foreach ($this->children as $child) { ?>
        <?
            $style = "float: left; width: ".(100/$this->imagesPerLine)."%;";
        ?>
        <div style="<?=$style;?>">
            <?=$this->component($child);?>
        </div>
        <?
            if ($i%$this->imagesPerLine == $this->imagesPerLine-1) {
                echo '<div style="clear:both;"></div>';
            }
            $i++;
        ?>
    <? } ?>
    <div style="clear:both;"></div>
</div>
