<div class="<?=$this->rootElementClass?>">
    <ul>
        <?php $i=0; foreach($this->links as $l) { ?>
            <li<?php if ($i == 0) { echo ' class="first"'; } ?>>
                <?=$this->componentLink($l)?>
            </li>
        <?php $i++; } ?>
    </ul>
</div>
