<?php foreach ($this->pageLinks as $l) { ?>
    <a class="digit<?=mb_strlen(html_entity_decode($l['text']));?><? if ($l['active']) { ?> active<? } ?>" href="<?= $l['href'] ?>" rel="<?= $l['rel'] ?>"><?= $l['text'] ?></a>
<?php } ?>
