<div class="<?=$this->cssClass;?> vpsTabs">
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
        ?>
        <div class="<?=$class;?> vpsTabsLink <? if ($i == 0) echo 'vpsTabsLinkActive'; ?>"><?= $child['title']; ?></div>
        <div class="<?=$class;?> vpsTabsContent <? if ($i == 0) echo 'vpsTabsContentActive'; ?>">
            <?=$this->component($child['data']);?>
        </div>
    <? $i++;
       } ?>
</div>
