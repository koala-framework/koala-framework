<? if (count($this->networks)) { ?>
<div class="<?=$this->cssClass?> <?=$this->iconSet?>">
    <?php foreach ($this->networks as $network) { ?>
    <a class="<?= $network['id'] ?>" href="<?= htmlspecialchars($network['url']) ?>" data-kwc-popup="blank">
        <? if ($network['icon']) { ?><img alt="<?= $network['name'] ?>" src="<?= $network['icon'] ?>" title="<?= $this->data->trlKwf('Bookmark at') ?> <?= $network['name'] ?>" /><? } ?>
    </a>
    <?php } ?>
</div>
<? } ?>
