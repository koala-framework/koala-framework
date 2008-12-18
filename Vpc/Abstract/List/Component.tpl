<div class="<?=$this->cssClass?>">
    <? $i = 0; ?>
    <? foreach ($this->children as $child) { ?>
        <?
            $class = '';
            if ($i == 0) $class .= 'vpcFirst ';
            if ($i == count($this->children)-1) $class .= 'vpcLast ';
            $class = trim($class);
            $i++;
        ?>
        <div class="<?= $class; ?>">
            <?=$this->component($child);?>
        </div>
    <? } ?>
</div>