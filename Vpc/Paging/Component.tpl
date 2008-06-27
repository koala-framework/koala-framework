<?php if (count($this->pageLinks) > 1) { ?>
<div class="<?=$this->cssClass?>">
    <span><?= trlVps('Page') ?>:</span>
    <?php foreach ($this->pageLinks as $l) { ?>
        <a href="<?= $l['href'] ?>" rel="<?= $l['rel'] ?>"<?php if ($l['active']) { ?> class="active"<?php } ?>><?= $l['text'] ?></a>
    <?php } ?>
</div>
<div class="webNewContentBox"></div>
<?php } ?>