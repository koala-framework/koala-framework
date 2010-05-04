<div class="<?=$this->cssClass?> vpsFadeElements">
    <input type="hidden" name="fadeSelector" class="fadeSelector" value="<?= $this->selector; ?>" />

    <? $i = 0; ?>
    <? foreach ($this->children as $child) { ?>
        <?
            $class = '';
            if ($i == 0) $class .= 'vpcFirst ';
            if ($i == count($this->children)-1) $class .= 'vpcLast ';
            if ($i % 2 == 0) {
                $class .= ' vpcEven';
            } else {
                $class .= ' vpcOdd';
            }
            $class = trim($class);
            $i++;
        ?>
        <div class="<?= $class; ?>">
            <?=$this->component($child);?>
        </div>
    <? } ?>
</div>