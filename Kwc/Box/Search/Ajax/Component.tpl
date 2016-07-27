<ul class="<?=$this->rootElementClass?>">
    <?php if ($this->foundNothing) { ?>
        <li class="noEntries"><?= $this->data->trlKwf('No entries found for search term &quot;{0}&quot;.', $this->queryValue) ?></li>
    <?php } else { ?>
    <?php foreach ($this->lists as $list) { ?>
        <li class="list">
            <?php if ($list['title']) { ?>
            <h2>
                <?= $list['title']; ?>
                <?php if ($list['showAllHref']) { ?>- <a href="<?= $list['showAllHref'] ?>"><?= $this->data->trlKwf('Show All') ?></a><?php } ?>
            </h2>
            <?php } ?>
            <div class="kwfUp-clear"></div>
            <?= $this->component($list['component']); ?>
            <?php if (!$list['title'] && $list['showAllHref']) { ?>
            <h1>
                <a href="<?= $list['showAllHref'] ?>"><?= $this->data->trlKwf('Show All') ?></a>
            </h1>
            <?php } ?>
        </li>
    <?php } ?>
    <?php } ?>
</ul>
