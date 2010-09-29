<div class="<?=$this->cssClass;?>">
    <? $i = 0; ?>
    <? foreach ($this->listItems as $child) { ?>
        <?
            $class = 'listItem ';
            if ($i == 0) $class .= 'vpcFirst ';
            if ($i == count($this->children)-1) $class .= 'vpcLast ';
            if ($i % 2 == 0) {
                $class .= 'vpcEven ';
            } else {
                $class .= 'vpcOdd ';
            }
            $class = trim($class);
            $i++;
        ?>
        <div class="<?=$class;?>">
            <?=$this->component($child['data']);?>
        </div>
    <? } ?>
    <div class="clear"></div>
</div>