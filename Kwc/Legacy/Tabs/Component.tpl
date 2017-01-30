<div class="<?=$this->rootElementClass?> kwfUp-kwfTabs" data-hash-prefix="<?=$this->data->componentId?>">
    <?php $i = 0; ?>
    <?php foreach ($this->listItems as $child) { ?>
        <?php
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
    <?php $i++;
    } ?>
</div>