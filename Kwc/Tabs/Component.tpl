<div class="<?=$this->rootElementClass?> kwfTabs">
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
        ?>
        <div class="<?=$class;?> kwfTabsLink"><?= $child['title']; ?></div>
        <div class="<?=$class;?> kwfTabsContent">
            <?=$this->component($child['data']);?>
        </div>
    <? $i++;
       } ?>
</div>
