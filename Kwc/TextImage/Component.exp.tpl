<div class="<?=$this->rootElementClass?>">
    <?php if ($this->image) { ?>
        <?php
            $style = "";
            if ($this->position == 'left') {
                $style = "float: left; margin-right: 20px;";
            } else if ($this->position == 'right') {
                $style = "float: right; margin-left: 20px;";
            }
        ?>
        <div class="image" style="<?=$style?>">
            <?=$this->component($this->image)?>
        </div>
    <?php } ?>
    <div class="text">
        <?=$this->component($this->text)?>
    </div>
    <div style="clear: both;"></div>
</div>
