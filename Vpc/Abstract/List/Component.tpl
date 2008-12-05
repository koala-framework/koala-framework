<div class="<?=$this->cssClass?>">
<? $i = 0; ?>
<?php foreach ($this->children as $child) { ?>
    <?
        $class = '';
        if ($i == 0) $class .= 'vpcFirst ';
        if ($i == count($this->children)-1) $class .= 'vpcLast ';
        $class = trim($class);
        $i++;
    ?>
    <div<? if($class) echo " class=\"$class\"";?>>
        <?=$this->component($child);?>
    </div>
<? } ?>
</div>