<?php foreach ($this->pageLinks as $l) { ?>
    <a<? if ($l['active']) { ?> class="active"<? } ?> href="<?=$l['href'];?>" rel="<?=$l['rel'];?>">
        <span<? if (!is_numeric($l['text'])) { ?> class="navigation"<? } ?>><?=$l['text'];?></span>
    </a>
<?php } ?>
