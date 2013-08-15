<? if (count($this->networks)) { ?>
<div class="<?=$this->cssClass?>">
    <?php foreach ($this->networks as $network) { ?>
    <a href="<?= htmlspecialchars($network['url']) ?>" rel="popup_blank">
        <img alt="<?= $network['name'] ?>" src="<?= $network['icon'] ?>" title="<?= $this->data->trlKwf('Bookmark at') ?> <?= $network['name'] ?>" />
    </a>
    <?php } ?>
</div>
<? } ?>