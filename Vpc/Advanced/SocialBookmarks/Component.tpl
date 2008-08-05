<div class="<?=$this->cssClass?>">
    <?php foreach ($this->services as $service) { ?>
    <a href="<?= $service['url'] ?>" target="_blank"><img alt="<?= $service['name'] ?>" src="<?= $service['icon'] ?>" title="<? trlVps('Bookmark bei') ?> <?= $service['name'] ?>" /></a>
    <?php } ?>
</div>