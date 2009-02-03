<?php foreach ($this->pageLinks as $l) { ?>
    <a href="<?= $l['href'] ?>" rel="<?= $l['rel'] ?>"<?php if ($l['active']) { ?> class="active"<?php } ?>><?= $l['text'] ?></a>
<?php } ?>
