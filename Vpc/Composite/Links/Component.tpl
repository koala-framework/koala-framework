<div class="<?=$this->cssClass;?>">
    <ul>
        <? $i = 0; ?>
        <? foreach ($this->children as $child) { ?>
            <?
                $class = '';
                if ($i == 0) $class .= 'vpcFirst ';
                if ($i == count($this->children)-1) $class .= 'vpcLast ';
                $class = trim($class);
                $i++;
            ?>
            <li class="<?=$class;?>"><?=$this->component($child);?></li>
        <? } ?>
    </ul>
</div>