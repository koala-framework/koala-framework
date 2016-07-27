<ul class="<?=$this->rootElementClass?>">
    <? if ($this->foundNothing) { ?>
        <li class="noEntries"><?= $this->data->trlKwf('No entries found for search term &quot;{0}&quot;.', $this->queryValue) ?></li>
    <? } else { ?>
    <? foreach ($this->lists as $list) { ?>
        <li class="list">
            <? if ($list['title']) { ?>
            <h2>
                <?= $list['title']; ?>
                <? if ($list['showAllHref']) { ?> <a href="<?= $list['showAllHref'] ?>"><?= $this->data->trlKwf('Show All') ?></a><? }?>
            </h2>
            <? } ?>
            <div class="kwfUp-clear"></div>
            <?= $this->component($list['component']); ?>
            <? if (!$list['title'] && $list['showAllHref']) { ?>
            <h1>
                <a href="<?= $list['showAllHref'] ?>"><?= $this->data->trlKwf('Show All') ?></a>
            </h1>
            <? } ?>
        </li>
    <? } ?>
    <? } ?>
</ul>
