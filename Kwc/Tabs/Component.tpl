<div class="<?=$this->rootElementClass?> kwfUp-kwfTabs">
    <? $i = 0; ?>
    <? foreach ($this->listItems as $child) { ?>
        <?
            $class = 'kwfUp-listItem ';
            if ($i == 0) $class .= 'kwfUp-kwcFirst ';
            if ($i == count($this->children)-1) $class .= 'kwfUp-kwcLast ';
            if ($i % 2 == 0) {
                $class .= 'kwfUp-kwcEven ';
            } else {
                $class .= 'kwfUp-kwcOdd ';
            }
            $class = trim($class);
        ?>
        <div class="<?=$class;?> kwfUp-kwfTabsLink"><?= $child['title']; ?></div>
        <div class="<?=$class;?> kwfUp-kwfTabsContent">
            <?=$this->component($child['data']);?>
        </div>
    <? $i++;
       } ?>
</div>
