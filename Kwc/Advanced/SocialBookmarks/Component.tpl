<?php if (count($this->networks)) { ?>
<div class="<?=$this->rootElementClass?> <?=$this->iconSet?>">
    <?php foreach ($this->networks as $network) { ?>
    <a class="<?= $network['id'] ?>" href="<?= Kwf_Util_HtmlSpecialChars::filter($network['url']) ?>" data-kwc-popup="blank">
        <?php if ($network['icon']) { ?><img alt="<?= $network['name'] ?>" src="<?= $network['icon'] ?>" title="<?= $this->data->trlKwf('Bookmark at') ?> <?= $network['name'] ?>" /><?php } ?>
    </a>
    <?php } ?>
</div>
<?php } ?>
