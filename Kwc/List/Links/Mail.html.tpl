<ul>
    <?php foreach ($this->children as $child) { ?>
        <li><?=$this->component($child);?></li>
    <?php } ?>
</ul>
