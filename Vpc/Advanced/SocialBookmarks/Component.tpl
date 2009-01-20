<div class="<?=$this->cssClass?>">
    <?php foreach ($this->services as $service) { ?>
    <a href="<?= htmlspecialchars($service['url']) ?>" target="_blank"><img alt="<?= $service['name'] ?>" src="<?= $service['icon'] ?>" title="<? trlVps('Bookmark at') ?> <?= $service['name'] ?>" /></a>
    <?php } ?>
</div>