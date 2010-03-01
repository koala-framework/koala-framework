<div class="<?=$this->cssClass?>">
    <?php foreach ($this->networks as $network) { ?>
    <a href="<?= htmlspecialchars($network['url']) ?>" target="_blank">
        <img alt="<?= $network['name'] ?>" src="<?= $network['icon'] ?>" title="<?= trlVps('Bookmark at') ?> <?= $network['name'] ?>" />
    </a>
    <?php } ?>
</div>