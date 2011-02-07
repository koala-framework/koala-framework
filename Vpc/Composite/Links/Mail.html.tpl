<ul>
    <? foreach ($this->children as $child) { ?>
        <li><?=$this->component($child);?></li>
    <? } ?>
</ul>
