<div class="<?=$this->cssClass?>">
    <?php foreach ($this->networks as $network) { ?>
    <a href="<?= htmlspecialchars($network['url']) ?>" rel="popup_blank">
        <img alt="<?= $network['name'] ?>" src="<?= $network['icon'] ?>" title="<?= trlVps('Bookmark at') ?> <?= $network['name'] ?>" />
    </a>
    <?php } ?>
</div>