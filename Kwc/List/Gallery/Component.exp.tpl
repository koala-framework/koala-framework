<?=$this->component($this->downloadAll); ?>
<div class="<?=$this->rootElementClass?>">
    <?php $i = 0; ?>
    <?php foreach ($this->children as $child) { ?>
        <?php
            $style = "float: left; width: ".(100/$this->imagesPerLine)."%;";
        ?>
        <div style="<?=$style;?>">
            <?=$this->component($child);?>
        </div>
        <?php
            if ($i%$this->imagesPerLine == $this->imagesPerLine-1) {
                echo '<div style="clear:both;"></div>';
            }
            $i++;
        ?>
    <?php } ?>
    <div style="clear:both;"></div>
</div>
