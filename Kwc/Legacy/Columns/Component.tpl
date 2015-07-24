<div class="<?=$this->rootElementClass?>">
    <? $i = 0; ?>
    <? foreach ($this->listItems as $child) { ?>
        <?
            $class = 'listItem ';
            if ($i == 0) $class .= 'kwcFirst ';
            if ($i == count($this->children)-1) $class .= 'kwcLast ';
            if ($i % 2 == 0) {
                $class .= 'kwcEven ';
            } else {
                $class .= 'kwcOdd ';
            }
            $class = trim($class);
            $i++;
        ?>
        <div class="column <?=$class;?>" style="width: <?=$child['width']?>">
            <?=$this->component($child['data']);?>
        </div>
    <? } ?>
    <div class="clear"></div>
</div>
